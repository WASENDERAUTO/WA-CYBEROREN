const Client = require('./Client.js');
const eventEmitter = require('./../../app/lib/Event.js');
const CampaignsDatabase = require('./../../app/database/campaigns.db.js');
const BulksDatabase = require('./../../app/database/bulks.db.js');

class Bulk extends CampaignsDatabase {

    constructor(velixs, session) {
        super();
        this.velixs = velixs;
        this.session = session;
        this.wacon = 'close';
        this.campaigns_runing = 'none';
        this.current_campaign = null;
        this.bulkdb = new BulksDatabase();
    }

    async mainHandler() {
        eventEmitter.on('wa.connection', async ({ session_id, status }) => {
            if (session_id !== this.session) return;
            this.wacon = status;
            if (status === 'open') {
                // console.log('triger-campaigns wa.connection open');
                return this.set_cron_schedule();
            } else {
                this.campaigns_runing = 'none';
            }
        });

        eventEmitter.on('campaigns', async (res) => {
            if (res !== this.session) return;
            // console.log('triger-campaigns');
            return this.set_cron_schedule();
        });
    }

    async bulks() {
        try {
            let getbulk = await new BulksDatabase().getBulk(this.current_campaign.id);
            if (getbulk.length === 0) {
                this.campaigns_runing = 'none';
                await new CampaignsDatabase().updateCampaign(this.current_campaign.id, 'completed');
                return;
            }
            let delay = this.current_campaign.delay * 1000;
            if (this.current_campaign.status === 'waiting') await new CampaignsDatabase().updateCampaign(this.current_campaign.id, 'processing');
            for (let i = 0; i < getbulk.length; i++) {
                let row = getbulk[i];
                if (this.campaigns_runing === 'none') break;
                if (this.wacon === 'close') {
                    this.campaigns_runing = 'none';
                    break
                }
                if (this.campaigns_runing !== row.campaign_id) {
                    // console.log('campaign changed');
                    this.campaigns_runing = 'none';
                    break
                }

                let client = new Client(this.velixs, row.receiver);
                // if is not whatsapp number
                if (!await client.isWhatsapp(row.receiver)) {
                    await this.bulkdb.updateBulk(row.id, 'invalid');
                    continue;
                }

                // console.log('Sending bulk to ' + row.receiver);
                await this.sendMessage({ ilsya: client, row: row, message: this.current_campaign.message });

                // if (i === getbulk.length - 1) {
                //     this.campaigns_runing = 'none';
                //     // console.log('Campaigns completed.');
                //     // await new CampaignsDatabase().updateCampaign(this.current_campaign.id, 'completed');
                //     break
                // }
                await new Promise(resolve => setTimeout(resolve, parseInt(delay)));
            }
        } catch (e) {
        }
    }

    async sendMessage({ ilsya, row, message }) {
        let data = JSON.parse(row.message);
        switch (row.message_type) {
            case "text":
                ilsya.sendText(this.filterMessage(data.message, row)).then(() => {
                    this.bulkdb.updateBulk(row.id, 'sent');
                }).catch((e) => {
                    console.log(e);
                    this.bulkdb.updateBulk(row.id, 'failed');
                });
                break
            case "media":
                let opts = { file: { mimetype: `${data.media_type}` } };
                ilsya.sendMedia(data.url, this.filterMessage(data.caption, row), opts).then(() => {
                    this.bulkdb.updateBulk(row.id, 'sent');
                }).catch((e) => {
                    this.bulkdb.updateBulk(row.id, 'failed');
                });
                break;
            case "button":
                ilsya.sendButton({
                    // image_url: data.image_url,
                    text: this.filterMessage(data.message, row),
                    footer: data.footer,
                    buttons: data.buttons
                }).then(() => {
                    this.bulkdb.updateBulk(row.id, 'sent');
                }).catch((e) => {
                    this.bulkdb.updateBulk(row.id, 'failed');
                });
                break
            case "list":
                if (row.receiver.includes('@g.us')) {
                    return this.bulkdb.updateBulk(row.id, 'invalid');
                }
                ilsya.sendListButton({
                    // image_url: data.image_url,
                    title: data.title,
                    text: this.filterMessage(data.message, row),
                    footer: data.footer,
                    button_text: data.buttonText,
                    sections: data.sections
                }).then(() => {
                    this.bulkdb.updateBulk(row.id, 'sent');
                }).catch((e) => {
                    this.bulkdb.updateBulk(row.id, 'failed');
                });
                break
        }
    }

    async set_cron_schedule() {
        this.current_campaign = await this.getCampaignsOn(this.session);
        if (this.current_campaign === null) {
            // console.log('Campaigns not found.');
            this.campaigns_runing = 'none';
            return;
        }

        if (this.campaigns_runing === this.current_campaign.id) return;
        this.campaigns_runing = this.current_campaign.id;
        let schedule = this.current_campaign.scheduled_at;
        schedule = new Date(schedule).getTime();
        // console.log(this.wacon);
        let schedule_interval = setInterval(async () => {
            if (this.campaigns_runing === 'none') {
                return clearInterval(schedule_interval);
            }

            if (this.wacon === 'close') {
                this.campaigns_runing = 'none';
                return clearInterval(schedule_interval);
            }

            if (new Date().getTime() >= schedule) {
                this.bulks();
                // console.log(new Date().getTime() >= schedule);
                return clearInterval(schedule_interval);
            }
            // console.log('Campaigns waiting.');
            await new Promise(resolve => setTimeout(resolve, 5000));
        }, 10000);
    }

    filterMessage(message, row){
        while (message.includes('{name}')) {
            message = message.replace('{name}', row.receiver_name);
        }
        while (message.includes('{number}')) {
            message = message.replace('{number}', row.receiver);
        }
        // ex string {{Hai|Hello}} then random choose
        const regex = (/\{\{(.+?)\}\}/g);
        let matches;
        while ((matches = regex.exec(message)) !== null) {
            let random = Math.floor(Math.random() * matches[1].split('|').length);
            message = message.replace(matches[0], matches[1].split('|')[random]);
        }
        return message
    }
}

module.exports = Bulk;
