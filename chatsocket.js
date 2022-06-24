const server = require('http').createServer()
const io = require('socket.io')(server)
const connection = require('./database.js');
const fs = require('fs')
const util = require('util')
const moment = require('moment');
const query = util.promisify(connection.query).bind(connection)
const { config } = require('./socket-server-config.js');

io.on('connection', client => {
    client.on('chat', async data => {
        console.log('chat1',data);
        // fs.open(`./chat_logs/chats_stream_id_${data.stream_id}.json`, function (err, fd) {
        //     if (err) {
        //         fs.writeFile(`./chat_logs/chats_stream_id_${data.stream_id}.json`, JSON.stringify([data]), function (err) {
        //             if (err) {
        //                 throw err;
        //             }
        //         })
        //     } else {
        //         fs.readFile(`./chat_logs/chats_stream_id_${data.stream_id}.json`, (err, d) => {
        //             if (err) throw err;
        //             let chats = !!String(d).match(/^\s*$/) ? [] : JSON.parse(d);
        //             chats.push(data)
        //             fs.writeFile(`./chat_logs/chats_stream_id_${data.stream_id}.json`, JSON.stringify(chats), function (err) {
        //                 if (err) {
        //                     throw err;
        //                 }
        //             })
        //         })
        //     }
        // })

        let isUpdateLastChat = false;

        if (data.chat_is_praise) {
            const checkLastChat = await query(`SELECT * FROM ${config.db_prefix}chat WHERE stream_id='${data.stream_id}' ORDER BY id DESC LIMIT 1`);
    
            if (checkLastChat.length > 0) {
                if (checkLastChat[0].chat_is_praise && checkLastChat[0].user_id == data.user_id) {
                    isUpdateLastChat = true;
                    let praiseCount = parseInt(checkLastChat[0].chat_praise_count);
                    praiseCount++;
    
                    await connection.query(`UPDATE ${config.db_prefix}chat SET chat_praise_count='${praiseCount}' WHERE id = '${checkLastChat[0].id}'`);
                }
            }
        }

        if (!isUpdateLastChat) {
            console.log('welcome');
            console.log(`INSERT INTO ${config.db_prefix}chat SET stream_id= ? , chat_json= ? , user_id= ? , timestamp= ?, chat_is_praise= ?, chat_praise_count= ?, date_created= ? `,[
                data.stream_id,
                // connection.escape(data.msg),
                data.msg,
                data.user_id,
                data.timestamp,
                (data.chat_is_praise !== undefined ? data.chat_is_praise : 0),
                (data.chat_is_praise !== undefined && data.chat_is_praise == 1 ? 1 : 0),
                moment().utcOffset('+0800').format('YYYY-MM-DD HH:mm:ss'),
            ]);


            await connection.query(`INSERT INTO ${config.db_prefix}chat SET stream_id= ? , chat_json= ? , user_id= ? , timestamp= ?, chat_is_praise= ?, chat_praise_count= ?, date_created= ? `,[
                data.stream_id,
                // connection.escape(data.msg),
                data.msg,
                data.user_id,
                data.timestamp,
                (data.chat_is_praise !== undefined ? data.chat_is_praise : 0),
                (data.chat_is_praise !== undefined && data.chat_is_praise == 1 ? 1 : 0),
                moment().utcOffset('+0800').format('YYYY-MM-DD HH:mm:ss'),
            ],function(error, results){});
        }

        // connection.query("SELECT * FROM bank_accounts WHERE dob = ? AND bank_account = ?",[
        //     req.body.dob,
        //     req.body.account_number
        //    ],function(error, results){});

        io.emit('chat', data)
        // console.log(data)
    });

    client.on('disconnect', () => {
        console.log("Disconnected1")
    });
})

server.listen(config.chat_port, () => {
    console.log('Server started on port ' + config.chat_port);
})
