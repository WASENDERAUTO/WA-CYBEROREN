const {
    default: WASocket,
    makeInMemoryStore,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestWaWebVersion,
} = require("@whiskeysockets/baileys");
const { logger } = require("../app/lib/myf.velixs.js");
const pino = require("pino");
const qrcode = require("qrcode");
const fs = require("fs");
const SessionsDatabase = require("../app/database/sessions.db.js");
const { Boom } = require("@hapi/boom");
const Message = require("./Client/MessageHandler.js");
const Bulk = require("./Client/Bulk.js");
const eventEmitter = require("./../app/lib/Event.js");
let sessionMap = new Map();

class SessionConnection extends SessionsDatabase {

    constructor(socket) {
        super();
        this.socket = socket;
        this.storagePath = __dirname + "/../storage";
        this.sessionPath = this.storagePath + "/sessions";
        this.time_out_qr = 0;
    }

    async getSession(session) {
        return sessionMap.get(session) ? sessionMap.get(session) : null
    }

    async deleteSession(session) {
        try {
            sessionMap.delete(session);
            if (fs.existsSync(`${this.sessionPath}/${session}`)) fs.rmSync(`${this.sessionPath}/${session}`, { force: true, recursive: true });
            logger("info", "[SESSION] SESSION DELETED : " + `${session} `);
        } catch (e) {
            logger("error", "[SESSION] SESSION DELETED ERROR : " + `${session} `);
        }
    }

    async generateQr(input, session) {
        let rawData = await qrcode.toDataURL(input, { scale: 8 });
        // wait 3 seconds
        setTimeout(() => {
            this.socket.emit(`servervelixs`, {
                status: true,
                code_message: "qr200",
                session_id: session,
                qr: rawData,
            });
        }, 2000);
        this.time_out_qr++;
        logger("info", "[SESSION] WAITING FOR THE SCAN QR : " + `${session} ` + `(${this.time_out_qr})`);
        this.socket.emit('logger', {
            session_id: session,
            type: 'info',
            message: `[SESSION] WAITING FOR THE SCAN QR ( ${this.time_out_qr} OF ${process.env.TIME_OUT_QR} ).`
        })
    }

    async autoStart() {
        let session = await this.table.findAll({ where: { status: 'CONNECTED' } });
        if (session.length > 0) {
            session.forEach(async (session) => {
                if (fs.existsSync(`${this.sessionPath}/${session.id}`)) {
                    logger("info", "[SESSION] AUTO START : " + `${session.session_name} `);
                    await this.createSession(session.id);
                } else {
                    logger("info", "[SESSION] AUTO START ERROR : " + `${session.session_name} `);
                    await this.updateStatus(session.id);
                }
            });
        }
    }

    async createSession(session) {
        var unknown_attempt = 0;
        const sessionDir = `${this.sessionPath}/${session}`;
        const storePath = `${this.sessionPath}/${session}/store_walix.json`;
        if (!fs.existsSync(sessionDir)) fs.mkdirSync(sessionDir, { recursive: true });
        let { state, saveCreds } = await useMultiFileAuthState(sessionDir);
        const { version, isLatest } = await fetchLatestWaWebVersion();
        const store = makeInMemoryStore({ logger: pino().child({ level: 'silent', stream: 'store' }) });

        const velixs = WASocket({
            printQRInTerminal: false,
            auth: state,
            logger: pino({ level: "silent" }),
            browser: ["WALazy", "Safari", "3.0"],
            version
        });

        try {
            store.readFromFile(storePath);
            let store_interval = setInterval(() => {
                try {
                    store.writeToFile(storePath);
                } catch (e) {
                    if (e.code === "ENOENT") {
                        clearInterval(store_interval)
                    }
                }
            }, 10000);

            store.bind(velixs.ev);
            velixs.chats = store.chats;
        } catch (e) {
            logger("error", "[SESSION] STORE ERROR : " + `${session} `);
        }

        sessionMap.set(session, { ...velixs, isStop: false }); // add session to map

        // event
        velixs.ev.on("creds.update", saveCreds);

        velixs.ev.on("connection.update", async (update) => {
            if (update.isNewLogin) {
                try {
                    if (await this.findSessionId(session)) {
                        await this.updateStatus(session, 'CONNECTED', velixs.authState.creds.me.id.split(":")[0]);
                    } else {
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'error',
                            message: `[DEVICE] DEVICE NOT FOUND, PLEASE REFRESH PAGE.`
                        })
                        this.socket.emit(`servervelixs`, {
                            status: true,
                            code_message: "device404",
                            session_id: session,
                            message: "DEVICE NOT FOUND.",
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        return this.deleteSession(session);
                    }
                    this.socket.emit(`logger`, {
                        session_id: session,
                        type: 'debug',
                        message: `[SESSION] NEW CONNECTED.`
                    })
                    this.socket.emit(`servervelixs`, {
                        status: true,
                        code_message: "sessionconnected",
                        session_id: session,
                        session: {
                            name: velixs.authState.creds.me.name,
                            number: velixs.authState.creds.me.id.split(":")[0],
                            platform: velixs.authState.creds.platform,
                            log: '',
                        }
                    });
                    return eventEmitter.emit('wa.connection', {
                        session_id: session,
                        status: 'open',
                    });
                } catch (e) { }
            } else {
                if (update.qr) {
                    try {
                        if (this.time_out_qr >= process.env.TIME_OUT_QR) {
                            velixs.ev.removeAllListeners("connection.update");
                            this.deleteSession(session);
                            logger("debug", "[SESSION] SESSION END : " + `${session}`);
                            this.socket.emit('logger', {
                                session_id: session,
                                type: 'debug',
                                message: `[SESSION] SESSION END, PLEASE REGENERATE QR CODE.`
                            })
                            this.socket.emit(`servervelixs`, {
                                status: true,
                                code_message: "regenerateqr",
                                session_id: session,
                                message: "QR Code Expired",
                            });
                            return;
                        }
                        this.generateQr(update.qr, session);
                    } catch (e) { }
                }
            }

            try {
                const { lastDisconnect, connection } = update;
                if (connection === "close") {
                    this.updateStatus(session);
                    eventEmitter.emit('wa.connection', {
                        session_id: session,
                        status: 'close',
                    });
                    const reason = new Boom(lastDisconnect?.error)?.output.statusCode
                    if (reason === DisconnectReason.badSession) {
                        this.socket.emit(`servervelixs`, {
                            code_message: "endsession",
                            session_id: session,
                            message: "Bad Session File.",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'error',
                            message: `[SESSION] BAD SESSION FILE, PLEASE REGENERATE QR CODE.`
                        });
                        logger("error", "[SESSION] BAD SESSION FILE : " + `${session}`, true);
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        return this.deleteSession(session);
                    } else if (reason === DisconnectReason.connectionClosed) {
                        logger("debug", "[SESSION] CONNECTION CLOSED, RECONNECTING : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "reconnect",
                            session_id: session,
                            message: "Connection Closed, Reconnecting...",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] CONNECTION CLOSED, RECONNECTING...`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        this.createSession(session);
                    } else if (reason === DisconnectReason.connectionLost) {
                        logger("debug", "[SESSION] CONNECTION LOST, RECONNECTING : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "reconnect",
                            session_id: session,
                            message: "Connection Lost, Reconnecting...",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] CONNECTION LOST, RECONNECTING...`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        this.createSession(session);
                    } else if (reason === DisconnectReason.connectionReplaced) {
                        logger("debug", "[SESSION] CONNECTION REPLACED, ANOTHER NEW SESSION OPENED, PLEASE CLOSE CURRENT SESSION FIRST : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "endsession",
                            session_id: session,
                            message: "Connection Replaced, Another New Session Opened.",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] CONNECTION REPLACED, PLEASE CLOSE CURRENT SESSION FIRST.`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        return this.deleteSession(session);
                    } else if (reason === DisconnectReason.loggedOut) {
                        logger("debug", "[SESSION] LOGGED OUT, PLEASE REGENERATE QR CODE : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "endsession",
                            session_id: session,
                            message: "Logged Out, Please Regenerate QR Code.",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] LOGGED OUT, PLEASE RESTART SESSION!`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        this.deleteSession(session, false);
                    } else if (reason === DisconnectReason.restartRequired) {
                        logger("debug", "[SESSION] RESTART REQUIRED, RESTARTING : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "reconnect",
                            session_id: session,
                            message: "Restart Required, Restarting...",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] RESTART REQUIRED, RESTARTING...`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        this.createSession(session);
                    } else if (reason === DisconnectReason.timedOut) {
                        logger("debug", "[SESSION] CONNECTION TIMED OUT, RECONNECTING : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "reconnect",
                            session_id: session,
                            message: "Connection TimedOut, Reconnecting...",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] CONNECTION TIMED OUT, RECONNECTING...`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        this.createSession(session);
                    } else {
                        logger("debug", "[SESSION] DISCONNECTED, RECONNECTING : " + `${session}`, true);
                        this.socket.emit(`servervelixs`, {
                            code_message: "endsession",
                            session_id: session,
                            message: "Disconnected, Unknown Reason.",
                        });
                        this.socket.emit('logger', {
                            session_id: session,
                            type: 'debug',
                            message: `[SESSION] DISCONNECTED, UNKNOWN REASON.`
                        });
                        velixs.ev.removeAllListeners("connection.update");
                        velixs.end();
                        if (unknown_attempt >= 10) {
                            unknown_attempt = 0;
                            this.deleteSession(session, false);
                        } else {
                            unknown_attempt++;
                            return this.createSession(session);
                        }
                    }
                } else if (connection == "open") {
                    await this.updateStatus(session, 'CONNECTED', velixs.authState.creds.me.id.split(":")[0]);
                    logger("debug", "[SESSION] CONNECTED : " + `${session}`);
                    this.socket.emit(`logger`, {
                        session_id: session,
                        type: 'debug',
                        message: `[SESSION] NEW CONNECTED.`
                    })
                    this.socket.emit(`servervelixs`, {
                        status: true,
                        code_message: "sessionconnected",
                        session_id: session,
                        session: {
                            name: velixs.authState.creds.me.name,
                            number: velixs.authState.creds.me.id.split(":")[0],
                            platform: velixs.authState.creds.platform,
                            log: '',
                        }
                    });
                    eventEmitter.emit('wa.connection', {
                        session_id: session,
                        status: 'open',
                    });
                }
            } catch (e) {
                console.log(e);
            }
        });

        velixs.ev.on("messages.upsert", async (chatUpdate) => {
            if (chatUpdate.type !== "notify") return;
            const message = new Message(velixs, chatUpdate.messages[0], session);
            message.mainHandler();
        });


        new Bulk(velixs, session).mainHandler();
    }

}

module.exports = SessionConnection;
