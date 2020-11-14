var gulp = require('gulp');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var nodemon = require('gulp-nodemon');
let uglify = require('gulp-uglify-es').default;
let del = require('del');
let imagemin = require('gulp-imagemin');

function sass_converter(cb) {        
    gulp.src('./scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            errorLogToConsole: true
        }))
        .on('error', console.error.bind(console))
        .pipe(autoprefixer({
            overrideBrowserslist: ['last 5 versions'],
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
function js_converter(cb) {
    gulp.src('./js/*.js')
        .pipe(gulp.dest('./public/js/'))
        .pipe(uglify())		
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./public/js/'));
	cb();
}
function watchSass() {
    gulp.watch('./scss/**/*', sass_converter);
	gulp.watch('./js/**/*', js_converter);	
	gulp.watch('./img/**/*', img_converter);
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

function clean() {
    return del('./public/');
}
function img_converter(cb) {
    gulp.src('./img/**/*.{jpg,png,svg,gif,ico,webp}')
        .pipe(
            imagemin({
                progressive: true,
                svgoPlugins: [{ removeViewBox: false }],
                interlaced: true,
                optimizationLavel: 3 // 0 to 7
            })
        )
        .pipe(gulp.dest('./public/img/'));
	cb();
}

let build = gulp.series(clean, gulp.parallel(js_converter, sass_converter, img_converter)); 
gulp.task('default', gulp.parallel(build, nodeM, watchSass));

/*
function defaultTask(cb) {    
    console.log("some text");
    cb();
}  
exports.default = defaultTask
*/