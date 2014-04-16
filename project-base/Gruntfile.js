module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		less: {
			admin: {
				files: {
					'web/assets/admin/styles/index.css': 'src/SS6/AdminBundle/Resources/styles/main.less'
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/admin/styles/index.css.map',
					sourceMapBasepath: 'web',
					sourceMapURL: 'index.css.map',
					sourceMapRootpath: '../../../'
				}
			},

			frontend: {
				files: {
					'web/assets/frontend/styles/index.css': 'src/SS6/FrontBundle/Resources/styles/main.less'
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/frontend/styles/index.css.map',
					sourceMapBasepath: 'web',
					sourceMapURL: 'index.css.map',
					sourceMapRootpath: '../../../'
				}
			}
		},


		legacssy: {
			admin: {
				options: {
					legacyWidth: 641,
					matchingOnly: true,
					overridesOnly: true
				},
				files: {
						'web/assets/admin/styles/index-ie8.css': 'web/assets/admin/styles/index.css'
				}
			},
			frontend: {
				options: {
					legacyWidth: 641,
					matchingOnly: true,
					overridesOnly: true
				},
				files: {
					'web/assets/frontend/styles/index-ie8.css': 'web/assets/frontend/styles/index.css'
				}
			}
		},


		watch: {
			admin: {
				files: ['src/SS6/AdminBundle/Resources/styles/**/*.less'],
				tasks: ['admin'],
				options: {
					livereload: true,
				}
			},
			frontend: {
				files: ['src/SS6/FrontBundle/Resources/styles/**/*.less'],
				tasks: ['frontend'],
				options: {
					livereload: true,
				}
			}
		}


	});
	
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-legacssy');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['less', 'legacssy']);

	grunt.registerTask('frontend', ['less:frontend', 'legacssy:frontend']);
	grunt.registerTask('admin', ['less:admin', 'legacssy:admin' ]);
};
