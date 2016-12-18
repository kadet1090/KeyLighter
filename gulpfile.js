const gulp         = require("gulp");
const sass         = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');

gulp.task("styles", function () {
    return gulp
        .src('./Styles/Html/*.scss')
        .pipe(sass())
        .pipe(autoprefixer({
            browsers: ['last 5 versions'],
            cascade: false
        }))
        .on('error', function(error) { console.log(error); this.emit('end') })
        .pipe(gulp.dest('./Styles/Html/dist/'));
});

gulp.task("default", ["styles"]);

gulp.task('watch', ['default'], function() {
    gulp.watch('Styles/Html/**/*', ['styles']);
});
