const express = require('express');
const { connectDatabase } = require('./database.js');
const routerApi = require('../routes/api.js');
const axios = require('axios');
const { logger } = require('../lib/myf.velixs.js');
const { loadCommands } = require('../config/commands.js');
const cors = require('cors');
const fs = require('fs');
const https = require('https');

class app {
    constructor() {
        this.app = express();
        this.plugins();
        this.routes();
        this.lv = '4.5.0';
    }

    plugins() {
        try{
            axios.get('https://raw.githubusercontent.com/ilsyaa/lazy-version/master/walazy.json',{
                httpsAgent: new https.Agent({
                    rejectUnauthorized: false
                })
            }).then(res => {
                if (this.lv != res.data.version) {
                    logger('warning', '[VERSION] NEW VERSION AVAILABLE: ' + res.data.version)
                    if (res.data.required_update) {
                        logger('warning', '[VERSION] REQUIRED UPDATE: ' + res.data.version)
                        process.exit(0);
                    }
                }
            });
        }catch(e){
          logger('error', '[AXIOS] FAILED TO CHECK VERSION: ' + e)
        }
        this.app.use(cors());
        this.app.use(express.json());
        this.app.use(express.urlencoded({ extended: true }));
        this.app.use(express.json())
        this.app.use((req, res, next) => {
            res.header('Access-Control-Allow-Origin', '*');
            res.set('Cache-Control', 'no-store');
            res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
            res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            next();
        });
        connectDatabase();
        loadCommands();
    }

    routes() {
        this.app.use('/api', routerApi);
    }
}

module.exports = app;
