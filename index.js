var express = require('express');

var app = express();
app.use('/public', express.static('public'));

app.set('views', 'views');

app.set('view engine', 'ejs');

app.get('/', function(req, res){
    res.render('home');
});

app.get('/test', function(req, res){
    res.render('test', {test: 'test text'});
});

app.get('/test/:id', function(req, res){
    res.send('ID = ' + req.params.id);
});

app.get('/testy', function(req, res){
    res.sendFile(__dirname + '/web/index.html');
});

app.listen(8000, () => console.log('Starting my server on NodeJS: http://127.0.0.1:8000'));
