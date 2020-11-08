require('dotenv').config();

var express = require('express');
var	port = process.env.PORT || 8000;
var	app = express();
var	reload = require('reload');
	
var gulp = require('gulp');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require('browser-sync').create();


function serverExpress() {
	app.use(express.static('public'));
	app.set('views', 'views');
	app.set('view engine', 'ejs');
	app.get('/', function(req, res){ res.render('home') });
	app.get('/test', function(req, res){ res.render('test', {test: process.env.TEST}) });
	app.get('/test/:id', function(req, res){ res.send('ID = ' + req.params.id) });
	app.get('/testy', function(req, res){ res.sendFile(__dirname + '/js/web/index.html') });
	app.listen(port, () => console.log(`Starting my server on NodeJS: Listening on ${ port }`));
	
	//watchSass();
	
	/*
	reload(app).then(function (reloadReturned) {
		// reloadReturned is documented in the returns API in the README
		//reloadReturned.reload();		  
	}).catch(function (err) {
		console.error('Reload could not start, could not start server/sample app', err)
	});
	*/

	//cb();
}

function sass_converter(cb) {    
    
    gulp.src('./scss/style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            errorLogToConsole: true
        }))
        .on('error', console.error.bind(console))
        .pipe(autoprefixer({
            overrideBrowserslist: ['last 2 versions'],
            casdcade: false
        }))
        .pipe(gulp.dest('./public/css/'))
        .pipe(sass({
            outputStyle: 'compressed'
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./public/css/'))
        //.pipe(browserSync.stream());

    cb();
}

function watchSass() {
    gulp.watch('./scss/**/*', sass_converter);
	gulp.watch('./**/*.html', browserReload);
    gulp.watch('./**/*.php', browserReload);
    gulp.watch('./**/*.js', browserReload);
    gulp.watch('./**/*.ejs', browserReload);
}

function syncBrowser(cb) {
    browserSync.init({
        server: {
            baseDir: "./views"
        },
        port: 3000
    });    

    cb();
}

function browserReload(cb) {
    //browserSync.reload();	
    
    cb();
}

//gulp.task('default', serverExpress);
gulp.task('default', gulp.parallel(serverExpress, watchSass));

//gulp.task('default', watchSass);
//gulp.task('default', gulp.parallel(syncBrowser, watchSass));



/*
function defaultTask(cb) {    
    console.log("some text");
    cb();
}  
exports.default = defaultTask
*/