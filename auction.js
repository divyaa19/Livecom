const express = require('express');
const app = express();
const server = require('http').createServer();
const io = require('socket.io')(server)
const db = require('./database.js');
const moment = require('moment');
const util = require('util')
const query = util.promisify(db.query).bind(db)
const bodyParser = require("body-parser");
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

const DB_PREFIX = 'oc_'

const cronArr = {};

function startSession(data,callback){
     db.query(`SELECT oc_product.*,oc_buy_mode_data.* FROM oc_product 
                            LEFT JOIN oc_buy_mode_data ON oc_buy_mode_data.product_id = oc_product.product_id
                            WHERE oc_product.product_id = '${data.product_id}'`, (err,rows) => {
        
        if(err) throw err;


        callback(null, rows)  
    });
}

io.on('connection', client => 
{
    client.on('push-auction-state', async(data) => {

        if(data.data.start){
           startSession(data.data.product,  function (err, productSession) {

    var insert_session = db.query(`INSERT INTO oc_product_session SET product_id = '${productSession[0].product_id}',stream_id='${productSession[0].stream_id}', bid_type='${productSession[0].buy_mode}', initial_price='${productSession[0].starting_price}', session_price='${productSession[0].starting_price}', min_bid='${productSession[0].bid_increment}' ,price_tick='${productSession[0].discount_interval}',tick_time='${productSession[0].duration}',tick_type='${productSession[0].discount_interval_type}',run_time='${productSession[0].duration}'`);
    
    var product_session = db.query(`SELECT * FROM oc_product_session WHERE product_id = '${insert_session.product_id}'`, (err, rows) => {
       client.emit('listen-to-auction-state', rows[0])
    });
           });
        }
    })

    client.on("listen-to-auction-state", async (product) => 
    {
        var product_session = db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product_id}'`, (err, rows) => {
            if(err){
                client.emit(listen-to-auction-state, {"status":"failed"})
            }
            client.emit('listen-to-auction-state', rows[0])
        });
    });

    client.on("start-timer", async (product) => 
    {
        db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' ORDER BY session_id DESC LIMIT 1`, (err, rows) => {
        db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' AND session_id = '${rows[0].session_id}'`, (err, timer) => {
            var timer = timer[0].run_time;

            var Countdown = setInterval(function(){
                client.emit('start-timer',timer);
                timer--

                console.log(timer);

                //when countdown hits 0
                if(timer === 0){
                    db.query(`SELECT oc_live_bid_history.customer_id,oc_customer.lastname, oc_customer.firstname
                                FROM oc_live_bid_history 
                                LEFT JOIN oc_customer ON oc_customer.customer_id = oc_live_bid_history.customer_id 
                                WHERE oc_live_bid_history.bid_session_id = '${rows[0].session_id}' ORDER BY oc_live_bid_history.amount DESC LIMIT 1`, (err, winner) =>{
                       client.emit('start-timer', 'Congratulation'+' '+winner[0].lastname +' '+ winner[0].firstname)
                       db.query(`UPDATE oc_stream_product_participants SET winner = '1' WHERE user_id = '${winner[0].customer_id}' AND session_id = '${rows[0].session_id}'`)
                       db.query(`UPDATE oc_product_session SET status = '4' WHERE session_id = '${rows[0].session_id}'`);
                        clearInterval(Countdown);
                    });
                }
            },1000);

            if(err){
                client.emit('start-timer', {"status":"failed"})
            }
            // client.emit('start-timer',timer[0].tick_time)
        });
        });
    });

    client.on("start-auction-low-timer", async (product) => 
    {
        db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' ORDER BY session_id DESC LIMIT 1`, (err, rows) => {
            if(err){
                client.emit('start-auction-low-timer',{"status":"failed"})
            }
        db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' AND session_id = '${rows[0].session_id}'`, (err, auction_low) => {

            if (auction_low[0].tick_type == 1){

                

            }
            var discount_interval = auction_low[0].tick_time * 1000;

            var duration = auction_low[0].run_time;

            var Countdown = setInterval(function(){
                client.emit('start-auction-low-timer',duration);
                duration--

                console.log(duration);

            },1000);

            client.emit('start-auction-low-timer',duration);

            if(duration > 0){

                var AuctionLowCountdown = setInterval(function(){
                    
                    var current_auction_price = auction_low[0].session_price - auction_low[0].price_tick;
    
                    db.query(`UPDATE oc_product_session SET cutoff_price = '${current_auction_price}' WHERE session_id = '${rows[0].session_id}'`)
    
                    client.emit('start-auction-low-timer',{"current price":current_auction_price})
    
                },discount_interval);
            }

            if(duration == 0){

                clearInterval(Countdown);

            }


            if(err){
                client.emit('start-auction-low-timer', {"status":"failed"})
            }
            // client.emit('start-timer',timer[0].tick_time)
        });
        });
    });

    client.on("set-bid", async (product) => 
    {
        //bid_amount
        var bid_amount = product.product.bid_amount;

        //product_id
        var product_id = product.product.product_id;

        //select latest data from producr_session
        var product_session = db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' ORDER BY session_id DESC LIMIT 1`, (err, rows) => {
            if(err){
                client.emit('listen-to-auction-state', {"status":"failed"})
            }

        db.query(`SELECT * FROM oc_stream_product_participants WHERE session_id = '${rows[0].session_id}' AND user_id = '${product.product.user_id}'`, (err, participant) =>
                {
                    //if not exists
                    if(participant.length === 0)
                    {
                        db.query(`INSERT INTO oc_stream_product_participants SET session_id = '${rows[0].session_id}',bid_type='${rows[0].bid_type}',user_id='${product.product.user_id}',winner='0'`);
                        console.log('user joined')
                    }
                })
            // client.emit('listen-to-auction-state', rows[0])
            //if auction_high
        if(rows[0].bid_type == 'auction_high')
        {
            //if(bid_amount) << session price
            if(bid_amount <= rows[0].session_price)
            {
               minBid = rows[0].session_price + rows[0].min_bid
                
               client.emit('listen-to-auction-state',JSON.stringify({status:false, message:"min bid is: " + minBid}))

            }else{

                // bid_amount+=rows[0].min_bid

                //update a new price on product_session
                db.query(`UPDATE ${DB_PREFIX}product_session SET session_price = '${bid_amount}' WHERE session_id = '${rows[0].session_id}'`);

                db.query(`SELECT * FROM oc_live_bid_history WHERE customer_id = '${product.product.user_id}'`, (err, columns) =>
                {
                    //if not exists
                    if(columns.length === 0)
                    {
                        db.query(`INSERT INTO oc_live_bid_history SET bid_session_id = '${rows[0].session_id}',customer_id='${product.product.user_id}',amount='${bid_amount}',product_id='${product.product.product_id}',quantity='1'`);
                        console.log('bid inserted')
                    }else{
                        db.query(`UPDATE oc_live_bid_history SET amount = '${bid_amount}' WHERE customer_id = '${product.product.user_id}' AND bid_session_id = '${rows[0].session_id}'`);
                        console.log('bid updated')
                    }
                })
                
                db.query(`SELECT * FROM oc_product_session WHERE product_id = '${product.product.product_id}' ORDER BY session_id DESC LIMIT 1`, (err, rows) =>
                {
                    //emit a new data from product_session
                    client.emit('listen-to-auction-state',JSON.stringify({status:true, product_session: rows[0]}))
                    console.log('price updated')
                })

            }
        }
        });
    });
});

server.listen(8000, () => {
  console.log('Server started on port 8000');
});