var fs = require('fs');

var http = require('http');

var server = http.createServer(function(req, res) {
    //var data = fs.readFileSync('web/index.html', 'utf8');
    res.writeHead(200, {'Content-Type': 'text/html; charset=utf-8'});
    //res.end('Hell Work<br>'+req.url);
    if (req.url=='/test') {
        res.end(fs.readFileSync('test/test.html', 'utf8'));
    }else {
        res.end(fs.readFileSync('web/index.html', 'utf8'));
    }
});

server.listen(8000, '127.0.0.1');
console.log('Starting my server on NodeJS: http://127.0.0.1:8000');