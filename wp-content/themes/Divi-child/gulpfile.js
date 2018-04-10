var gulp 		= require('gulp'),
	del			= require('del'),
	rename 		= require('gulp-rename'),
	sass 		= require('gulp-sass'),
	sourcemaps 	= require('gulp-sourcemaps'),
	minifyCSS	= require('gulp-cssnano'),
	uglify 		= require('gulp-uglify'),
	browserSync = require('browser-sync').create();

var assets = 'assets/',
	assetsCSS = assets + 'css/scss/',
	assetsCSSBuild = assets + 'css/',
	assetsJS = assets + 'js/src/',
	assetsJSBuild = assets + 'js/';

gulp.task('clean', function(cb) {
	del(assetsJSBuild + '*.js', cb);
});

// Compile Sass and check for errors, move to dist
gulp.task('compile-sass', function(cb) {
	gulp.src(assetsCSS + '**/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass({
			precision: 10,
			includePaths: ['node_modules/susy/sass']
		}).on('error', sass.logError))
		.pipe(rename({ suffix: '.min' }))
		.pipe(minifyCSS({ compatibility: 'ie8' }))
		.pipe(sourcemaps.write('./maps'))
		.pipe(gulp.dest(assetsCSSBuild))
		.pipe(browserSync.stream({match: '**/*.css'}));

	// Clean out any conflicted files.
	del(assetsCSSBuild + '*conflicted copy*');

	cb();
});

// Grab any files which haven't been minified, do so and move
gulp.task('compile-js', function(cb) {
	gulp.src([assetsJS + '*.js', '!' + assetsJS + '*.min.js'])
		.pipe(rename({ suffix: '.min' }))
		.pipe(uglify())
		.pipe(gulp.dest(assetsJSBuild));

	cb();
});

gulp.task('browser-sync', ['compile', 'automate'], function() {
	browserSync.init({
		proxy: 'wordpress.mac',
		open: true
	});
});

gulp.task('compile', ['compile-sass', 'compile-js'], function() {
	// Do everything
});

gulp.task('automate', function() {
	// Watch for any changes on Sass/JS files
	gulp.watch(assetsCSS + '**/*.scss', ['compile-sass']);
	gulp.watch(assetsJS + '*.js', ['compile-js']);
});

gulp.task('default', ['compile', 'automate']);
