const gulp  = require('gulp');
const concat = require('gulp-concat');
let sass = require('gulp-sass');

sass.compiler = require('node-sass');

function buildCss()
{
    return gulp.src(['./Resources/Private/Css/**/*.css', './Resources/Private/Css/**/*.scss'])
        .pipe(concat('main.scss'))
        .pipe(gulp.dest('./Resources/Public/Css/'));
}

function buildSass()
{
    return gulp.src('./Resources/Public/Css/main.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./Resources/Public/Css/'));
}

function buildJs()
{
    return gulp.src('./Resources/Private/JavaScript/**/*.js')
        .pipe(concat('main.js'))
        .pipe(gulp.dest('./Resources/Public/JavaScript/'));
}

function pipeJsMapFiles()
{
    return gulp.src('./Resources/Private/JavaScript/**/*.map')
        .pipe(gulp.dest('./Resources/Public/JavaScript/'));
}

function pipeCssMapFiles()
{
    return gulp.src('./Resources/Private/Css/**/*.map')
        .pipe(gulp.dest('./Resources/Public/Css/'));
}

exports.default = gulp.series(buildCss, buildSass, buildJs, pipeJsMapFiles, pipeCssMapFiles);