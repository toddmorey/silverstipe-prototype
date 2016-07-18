'use strict';

var gulp = require('gulp');

// BrowserSync isn't a gulp package, and needs to be loaded manually
var browserSync = require('browser-sync');
// define a variable that BrowserSync uses in it's function
var bs;
// command for reloading webpages using BrowserSync
var reload = browserSync.reload;

// sass (css compliation)
var sass = require('gulp-sass');


// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src("scss/**/*.*")
        .pipe(sass({includePaths: [
            'scss/**/',
            'bower_components/susy/sass']
          , errLogToConsole: true}))
        .pipe(gulp.dest("themes/proto/css"))
        .pipe(browserSync.stream());
});

gulp.task('start-browserSync', function () {
  bs = browserSync({
    notify: true,
    proxy: {
        target: "http://proto.dev",
    }
  });
});

// Watch content and templates to rebuild on change
gulp.task('watch', function () {
  gulp.watch(['scss/**/*.*'], ['sass']);
  gulp.watch(['themes/**/*.ss'], reload);
  gulp.watch(['themes/javascript/*.js'], reload);
  gulp.watch(['yaml/**/*.*'], reload);
});

// Default task to start site and serve it
gulp.task('default', ['sass', 'start-browserSync', 'watch']);
