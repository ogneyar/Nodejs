//require('fs')

var http = require('http');

var server = http.createServer(function(req, res) {
    res.writeHead(200, {'Content-Type': 'text/plain; charset=utf-8'});
    res.end('Hell Work');
});

server.listen(3000, '127.0.0.1');
console.log('Отслеживание порта 3000');