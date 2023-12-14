// const Serialize = require("./Serialize.js");
const serialize = require('./SerializeBeta.js');
const RespondersDatabase = require("../../app/database/responders.db.js");
const SessionsDatabase = require("../../app/database/sessions.db.js");
const Client = require("./Client.js");
const { logger } = require("../../app/lib/myf.velixs.js");
const axios = require("axios");
const { commands, checkSessionCommands } = require("../../app/config/commands.js");

class Message {
    constructor(velixs, msg, session) {
        // super();
        this.session = session;
        this.velixs = velixs;
        this.msg = msg;
        this.isResponder = 0;
        this.isResponder_contains = false;
    }

    async mainHandler() {
        try {
            if (!this.msg) return;
            const message = this.msg;
            if (message.key && message.key.remoteJid === "status@broadcast") return;
            if (!message.message) return;
            const m = await serialize(this.velixs, message)
            const ilsya = new Client(this.velixs, m.from);
            // const CMD = m.command ? m.command : null;
            const CMD = null;
            if (!CMD) return this.messageHandler(m, ilsya);
        } catch (error) {
            // console.log(error);
        }
    }

    async messageHandler(m, ilsya) {
        let isGroup = m.isGroup;
        let isMe = m.fromMe;
        const body = (m.type == "buttonsResponseMessage") ? m.message[m.type].selectedButtonId : (m.type == "listResponseMessage") ? m.message[m.type].singleSelectReply.selectedRowId : (m.type == "templateButtonReplyMessage") ? m.message[m.type].selectedId : m.text
        const args = body.trim().split(/ +/).slice(1)
        const commandbody = body.trim().split(/ +/).shift().toLowerCase()
        if(body == '') return;
        // webhook ---------------------------------- start
        let getwebook = await new SessionsDatabase().getWebhook(this.session);
        if (getwebook) {
            if (this.validateUrl(getwebook)) {
                axios.post(getwebook, {
                    message: body,
                    from: m.from,
                    isGroup: isGroup,
                    isMe: isMe,
                }).then((dt) => {
                    let res = JSON.parse(JSON.stringify(dt.data));
                    if (res.data != 'false') {
                        let row = JSON.parse(res.data);
                        let data = row.message;
                        switch (row.message_type) {
                            case "text":
                                ilsya.sendText(data.message)
                                break
                            case "media":
                                let opts = { file: { mimetype: `${data.media_type}` } };
                                ilsya.sendMedia(data.url, data.caption, opts)
                                break;
                        }
                    }
                }).catch((e) => {
                    // console.log(e);
                });
            }
        }
        // webhook ---------------------------------- end

        // auto responder ---------------------------------- start

        this.isResponder = await new RespondersDatabase().findAutoResponder({
            keyword: body,
            session_id: this.session,
        });

        if(this.isResponder.length == 0) {
            this.isResponder = await new RespondersDatabase().finAll({
                session_id: this.session,
            });

            this.isResponder_contains = true;
        }

        if (this.isResponder.length > 0) {
            this.isResponder.forEach(row => {
                if(this.isResponder_contains){
                    if(!body.includes(row.keyword)){
                        return;
                    }
                }

                // json_decode
                let data = JSON.parse(row.message);
                if (row.reply_when === "group" && !isGroup) return;
                if (row.reply_when === "personal" && isGroup) return;
                switch (row.message_type) {
                    case "text":
                        ilsya.sendText(data.message, data.quoted? m : '')
                        break
                    case "media":
                        let opts = { file: { mimetype: `${data.media_type}` } };
                        ilsya.sendMedia(data.url, data.caption, opts, data.quoted? m : '')
                        break;
                    case "button":
                        ilsya.sendButton({
                            // image_url: data.image_url,
                            text: data.message,
                            footer: data.footer,
                            buttons: data.buttons
                        })
                        break
                    case "list":
                        ilsya.sendListButton({
                            // image_url: data.image_url,
                            title: data.title,
                            text: data.message,
                            footer: data.footer,
                            button_text: data.buttonText,
                            sections: data.sections
                        })
                        break
                }
            });
        }
        // auto responder ---------------------------------- end


        // command
        try {
            let cmd = Array.from(commands.values()).find((v) => v.cmd.find((x) => x.toLowerCase() == commandbody.toLowerCase()));
            if (cmd) {
                if (cmd.global_feature) {
                    cmd.run(this.velixs, m, args);
                } else {
                    if (checkSessionCommands(this.session, cmd.name)) {
                        cmd.run(this.velixs, m, args);
                    }
                }
            }
        } catch (e) {
            // console.log(e);
        }

    }

    async validateUrl(inputUrl) {
        try {
            new URL(inputUrl);
            return true;
        } catch (error) {
            return false;
        }
    }
}

module.exports = Message;
