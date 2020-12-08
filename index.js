require('dotenv').config();

var express = require('express');
var	port = process.env.PORT || 8000;
var	app = express();
	
var http = require('http');
var	reload = require('reload');

app.use(express.static('public'));
app.set('views', 'views');
app.set('view engine', 'ejs');
app.get('/', function(req, res){
    res.render('home');
});
app.get('/test', function(req, res){
    res.render('test', {test: process.env.TEST});
});
app.get('/test/:id', function(req, res){
    res.send('ID = ' + req.params.id);
});
app.get('/testy', function(req, res){
    res.sendFile(__dirname + '/jstest/web/home.html');
});
app.get('/hello', function(req, res){
    //res.sendFile(__dirname + '/views/hello.html');
	res.render('hello');
});

app.get('/diplom', function(req, res){
    res.sendFile(__dirname + '/diplom/index.html');	
});
app.get('/diplom/shop', function(req, res){
    res.sendFile(__dirname + '/diplom/shop/shop.php');	
});
app.get('/diplom/admin', function(req, res){
    res.sendFile(__dirname + '/diplom/admin/admin.php');	
});


var server = http.createServer(app);
reload(app).then(function () {	
	server.listen(port, () => console.log(`Starting my server on NodeJS: Listening on ${ port }`));
}).catch(function (err) {
	console.error('Reload could not start, could not start server/sample app', err);
});
