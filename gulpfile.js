const gulp         = require("gulp");
const sass         = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');

gulp.task("styles", function () {
    return gulp
        .src('./Styles/Html/*.scss')
        .pipe(sass())
        .pipe(autoprefixer({
            cascade: false
        }))
        .on('error', function(error) { console.log(error); this.emit('end') })
        .pipe(gulp.dest('./Styles/Html/dist/'));
});

gulp.task("default", gulp.series('styles'));

gulp.task('watch', gulp.series('default', function() {
    gulp.watch('Styles/Html/**/*', ['styles']);
}));
