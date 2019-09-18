const gulp  = require('gulp');
const rename = require('gulp-rename');
const concat = require('gulp-concat');

function build()
{
    return gulp.src('./Resources/Private/JavaScript/**/*.js')
        .pipe(concat('main.js'))
        .pipe(gulp.dest('./Resources/Public/JavaScript/'));
}

exports.default = gulp.series(build);