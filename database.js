var mysql = require('mysql2')
var connection = mysql.createConnection({
    host: '127.0.0.1',
    user: 'root',
    password: '',
    database: 'livecom_server'
  });

connection.connect(function(err) {
  if (err) throw err;
  console.log('Database is connected successfully !');
});
module.exports = connection;
