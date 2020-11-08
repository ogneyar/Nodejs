require('dotenv').config();

var express = require('express'),
	port = process.env.PORT || 8000,
	app = express(),
	reload = require('reload');

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
    res.sendFile(__dirname + '/js/web/index.html');
});

reload(app).then(function (reloadReturned) {
	// reloadReturned is documented in the returns API in the README
 
	app.listen(port, () => console.log(`Starting my server on NodeJS: Listening on ${ port }`));
  
}).catch(function (err) {
	console.error('Reload could not start, could not start server/sample app', err);
});



/*
const express = require('express')
const PORT = process.env.PORT || 8000

express()
  .use(express.static('public'))
  .set('views', 'views')
  .set('view engine', 'ejs')
  .get('/', (req, res) => res.render('home'))
  .get('/test', (req, res) => res.render('test', {test: 'test text'}))
  .listen(PORT, () => console.log('Starting my server on NodeJS: http://127.0.0.1:8000'))
*/

