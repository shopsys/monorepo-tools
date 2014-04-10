module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),


		less: {
			admin: {
				files: {
					'web/assets/admin/index.css': 'web/assets/admin/styles/bootstrap.less'
				}
			},

			admin_mini: {
				files: {
					'web/assets/admin/index.min.css': 'web/assets/admin/index.css' 
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/admin/index.css.map',
					sourceMapRootpath: 'web/assets/admin/',
					sourceMapURL: 'web/assets/admin/index.css.map'
				}
			},

			frontend: {
				files: {
					'web/assets/frontend/index.css': 'web/assets/frontend/styles/main.less'
				}
			},

			frontend_mini: {
				files: {
					'web/assets/frontend/index.min.css': 'web/assets/frontend/index.css' 
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
			admin: {
				options: {
					legacyWidth: 641,
					matchingOnly: true,
					overridesOnly: true
				},
				files: {
						'web/assets/admin/index-ie8.css': 'web/assets/admin/index.css'
				}
			},
			frontend: {
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


		watch: {
			frontend: {
				files: ['web/assets/frontend/styles/**/*.less'],
				tasks: ['frontend'],
				options: {
					livereload: true,
				}
			},
			admin: {
				files: ['web/assets/admin/styles/**/*.less'],
				tasks: ['admin'],
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


	grunt.registerTask('frontend', ['less:frontend', 'less:frontend_mini', 'legacssy:frontend']);
	grunt.registerTask('admin', ['less:admin', 'less:admin_mini','legacssy:admin' ]);
};