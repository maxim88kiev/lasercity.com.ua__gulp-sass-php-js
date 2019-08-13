const del = require(`del`);
const gulp = require(`gulp`);
const sass = require(`gulp-sass`);
const plumber = require(`gulp-plumber`);
const postcss = require(`gulp-postcss`);
const autoprefixer = require(`autoprefixer`);
const mqpacker = require(`css-mqpacker`);
const minify = require(`gulp-csso`);

const rename = require(`gulp-rename`);
const imagemin = require(`gulp-imagemin`);

const browserSync = require('browser-sync');

gulp.task('style', function() {
  return gulp.src('app/sass/style.scss').
  pipe(plumber()).
  pipe(sass()).
  pipe(postcss([
    autoprefixer({
      browsers: [
        `last 1 version`,
        `last 2 Chrome versions`,
        `last 2 Firefox versions`,
        `last 2 Opera versions`,
        `last 2 Edge versions`
      ]
    }),
    mqpacker({sort: true})
  ])).
  pipe(minify()).
  pipe(rename(`style.min.css`)).
  pipe(gulp.dest(`app/SERVER/tpl_lasercity/css`)).
  pipe(browserSync.reload({stream: true}))
});

gulp.task('style-articles', function() {
  return gulp.src('app/sass/style-articles.scss').
  pipe(plumber()).
  pipe(sass()).
  pipe(postcss([
    autoprefixer({
      browsers: [
        `last 1 version`,
        `last 2 Chrome versions`,
        `last 2 Firefox versions`,
        `last 2 Opera versions`,
        `last 2 Edge versions`
      ]
    }),
    mqpacker({sort: true})
  ])).
  pipe(minify()).
  pipe(rename(`style-articles.min.css`)).
  pipe(gulp.dest(`app/SERVER/tpl_lasercity/css`)).
  pipe(browserSync.reload({stream: true}))
});

gulp.task('browser-sync', function() {
  browserSync({
    server: {
      baseDir: 'app'
    },
    notify: false
  });
});

gulp.task('scripts', function() {
  return gulp.src(['app/SERVER/tpl_lasercity/js/*.js'])
    .pipe(browserSync.reload({ stream: true }))
});

gulp.task('code', function() {
  return gulp.src('app/*.html')
    .pipe(browserSync.reload({ stream: true }))
});

gulp.task('code-articles', function() {
  return gulp.src('app/articles/*.html')
    .pipe(browserSync.reload({ stream: true }))
});

gulp.task('watch', function() {
  gulp.watch('app/sass/**/*.scss', gulp.parallel('style'));
  gulp.watch('app/*.html', gulp.parallel('code'));
  gulp.watch(['app/SERVER/tpl_lasercity/js/*.js'], gulp.parallel('scripts'));
});

gulp.task('watch-articles', function() {
  gulp.watch('app/sass/blocks/articles/*.scss', gulp.parallel('style-articles'));
  gulp.watch('app/articles/*.html', gulp.parallel('code-articles'));
  gulp.watch(['app/SERVER/tpl_lasercity/js/*.js'], gulp.parallel('scripts'));
});

gulp.task('main', gulp.parallel('style', 'browser-sync', 'watch'));

gulp.task('articles', gulp.parallel('style-articles', 'browser-sync', 'watch-articles'));