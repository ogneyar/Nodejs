var gulp = require('gulp');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var nodemon = require('gulp-nodemon');


function sass_converter(cb) {        
    gulp.src('./scss/*.scss')
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
        .pipe(gulp.dest('./public/css/'));        
    cb();
}

function watchSass() {
    gulp.watch('./scss/**/*', sass_converter);
}

function nodeM(done) {
	nodemon({
		script: 'index.js', 
		//tasks: ['watchSass'], 
		ext: 'js html ejs css', 
		env: { 'PORT': '8000' }, 
		done: done
	});
}

gulp.task('default', gulp.parallel(nodeM, watchSass));

/*
function defaultTask(cb) {    
    console.log("some text");
    cb();
}  
exports.default = defaultTask
*/