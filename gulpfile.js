var gulp = require('gulp');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require('browser-sync').create();


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
        .pipe(browserSync.stream());

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
            baseDir: "./"
        },
        port: 3000
    });    

    cb();
}

function browserReload(cb) {
    browserSync.reload();
    
    cb();
}

//gulp.task('default', gulp.parallel(syncBrowser, watchSass));

gulp.task('default', watchSass);


/*
function defaultTask(cb) {    
    console.log("some text");
    cb();
}  
exports.default = defaultTask
*/