module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),


		less: {
			default: {
				files: {
					"web/assets/frontend/index.css": "web/assets/frontend/styles/main.less"
				}
			},
			minified: {
				files: {
					'web/assets/frontend/index.min.css': 'web/assets/frontend/styles/main.less' 
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/frontend/index.css.map',
					sourceMapRootpath: 'web/assets/frontend/',
					sourceMapURL: 'web/assets/frontend/index.css.map' //url primo
				}
			}
		},


		legacssy: {
			default: {
				options: {
					legacyWidth: 641,
					matchingOnly: true,
					overridesOnly: true
				},
				files: {
			  		'web/assets/frontend/index-ie8.css': 'web/assets/frontend/index.css'
				}
			}
		},


		// 'grunt watch' hlida zmeny v souborech a provadi vyseuvedene tasky
		watch: {
			files: ['web/assets/frontend/styles/**/*.less'],
			tasks: ['less', 'legacssy'],
			options: {
				livereload: true,
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-legacssy');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ["less", "legacssy"]);
	grunt.registerTask('minified', ["less"]);
};