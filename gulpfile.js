var gulp = require('gulp'),
  rename = require('gulp-rename'),
  cssmin = require('gulp-minify-css'),
  less = require('gulp-less'),
  use_sourcemaps = false;

// LESS compilation
gulp.task('less', function () {
  var pipe = gulp.src('./less/style.less');
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.init());
  }
  pipe = pipe
    .pipe(less())
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}));
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.write('./maps'));
  }
  return pipe
    .pipe(gulp.dest('./dist/'))
    .on('error', errorHandler);
});

gulp.task('default', [
  'less',
]);

gulp.task('watch', function () {
  gulp.watch('./less/**/*', ['less']);
});

// Handle the error
function errorHandler(error) {
  console.log(error.toString());
  this.emit('end');
}
