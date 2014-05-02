module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		less: {
			admin: {
				files: {
					'web/assets/admin/styles/index.css': 'src/SS6/ShopBundle/Resources/styles/admin/main.less'
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
					'web/assets/frontend/styles/index.css': 'src/SS6/ShopBundle/Resources/styles/front/main.less'
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

		sprite: {
			admin: {
				src: 'web/assets/admin/images/icons/*.png',
				dest: 'web/assets/admin/images/icons/*.png',
				destImg: 'web/assets/admin/images/sprites/sprite.png',
				destCSS: 'src/SS6/ShopBundle/Resources/styles/admin/libs/sprites.less',
				imgPath: '../images/sprites/sprite.png',
				algorithm: 'binary-tree',
				padding: 50,
				engine: 'auto',
				cssFormat: 'css',
				cssVarMap: function (sprite) {
					sprite.name = 'sprite.sprite-' + sprite.name;
				},
				engineOpts: {
					imagemagick: true
				},
				imgOpts: {
					format: 'png',
					quality: 90,
					timeout: 10000
				},
				cssOpts: {
					functions: false,
					cssClass: function (item) {
							return '.' + item.name;
					}
				}
			},
			frontend: {
				src: 'web/assets/frontend/images/icons/*.png',
				dest: 'web/assets/frontend/images/icons/*.png',
				destImg: 'web/assets/frontend/images/sprites/sprite.png',
				destCSS: 'src/SS6/ShopBundle/Resources/styles/front/libs/sprites.less',
				imgPath: '../images/sprites/sprite.png',
				algorithm: 'binary-tree',
				padding: 50,
				engine: 'auto',
				cssFormat: 'css',
				cssVarMap: function (sprite) {
					sprite.name = 'sprite.sprite-' + sprite.name;
				},
				engineOpts: {
					imagemagick: true
				},
				imgOpts: {
					format: 'png',
					quality: 90,
					timeout: 10000
				},
				cssOpts: {
					functions: false,
					cssClass: function (item) {
							return '.' + item.name;
					}
				}
			}
		},

		watch: {
			admin: {
				files: ['src/SS6/ShopBundle/Resources/styles/admin/**/*.less', 'web/assets/admin/images/icons/**/*.png'],
				tasks: ['admin'],
				options: {
					livereload: true,
				}
			},
			frontend: {
				files: ['src/SS6/ShopBundle/Resources/styles/front/**/*.less', 'web/assets/frontend/images/icons/**/*.png'],
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
	grunt.loadNpmTasks('grunt-spritesmith');

	grunt.registerTask('default', ['sprite', 'less', 'legacssy']);

	grunt.registerTask('frontend', ['sprite:frontend', 'less:frontend', 'legacssy:frontend']);
	grunt.registerTask('admin', ['sprite:admin','less:admin', 'legacssy:admin' ]);
};
