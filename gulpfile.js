var gulp = require('gulp'),
  rename = require('gulp-rename'),
  concat = require('gulp-concat'),
  uglify = require('gulp-uglify'),
  cssmin = require('gulp-minify-css'),
  jshint = require('gulp-jshint'),
  less = require('gulp-less'),
  imagemin = require('gulp-imagemin'),
  spritesmith = require('gulp.spritesmith'),
  use_sourcemaps = false;


// Concat and minify JS, reading map.json
gulp.task('js', function () {
  var map = require('./js/map.json'), list = [];
  for (var i in map) {
    if (map.hasOwnProperty(i) && map[i]) {
      // Make relative to drupal path
      list.push('../../../../' + i);
    }
  }
  var pipe = gulp.src(list);
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.init());
  }
  pipe = pipe.pipe(concat('script.min.js'))
    .pipe(uglify());
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.write('./maps'));
  }
  return pipe.pipe(gulp.dest('./dist/'))
    .on('error', errorHandler);
});

// Verify JS syntax
gulp.task('jshint', function () {
  return gulp.src([
    './js/*.js',
    '!./js/*.min.js',
    '../../modules/**/*.js'
  ])
    .pipe(jshint())
    .pipe(jshint.reporter('default'))
    .pipe(jshint.reporter('fail'))
    .on('error', errorHandler);
});

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

// Optimisation des images
gulp.task('images', function () {
  return gulp.src(['./img*/*', './dist*/sprite.png'])
    .pipe(imagemin({
      progressive: true
    }))
    .pipe(gulp.dest('.')).on('error', errorHandler);
});


gulp.task('sprite', function () {
  var spriteData =
    gulp.src('img/sprite/*.*') // source path of the sprite images
      .pipe(spritesmith({
        imgName: 'sprite.png?' + (new Date).getTime(),
        cssName: 'sprite.less',
        algorithm: 'binary-tree'
      }));

  spriteData.img.pipe(rename('sprite.png')).pipe(gulp.dest('./dist/')); // output path for the sprite
  spriteData.css.pipe(gulp.dest('./less/')); // output path for the CSS
});

gulp.task('default', [
  'jshint',
  'js',
  'sprite',
  'images',
  'less',
]);

gulp.task('watch', function () {
  gulp.watch(['./js/*', '../../modules/**/*.js'], [
    'js',
    'jshint'
  ]);
  gulp.watch('./less/**/*', ['less']);
  gulp.watch('./img/*', ['images', 'sprite', 'less']);
  gulp.watch(['./img/*', '!./img/sprite.png'], ['images']);
  gulp.watch(['./img/*', '!./img/sprite.png'], ['sprite', 'images', 'less']);
});

// Handle the error
function errorHandler(error) {
  console.log(error.toString());
  this.emit('end');
}
