module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		less: {
			admin: {
				files: {
					'web/assets/admin/styles/index.css': 'src/SS6/ShopBundle/Resources/styles/common/admin/main.less'
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

			frontend1: {
				files: {
					'web/assets/frontend/styles/index1.css': 'src/SS6/ShopBundle/Resources/styles/common/front/main.less'
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/frontend/styles/index1.css.map',
					sourceMapBasepath: 'web',
					sourceMapURL: 'index1.css.map',
					sourceMapRootpath: '../../../'
				}
			},

			frontend2: {
				files: {
					'web/assets/frontend/styles/index2.css': 'src/SS6/ShopBundle/Resources/styles/domain2/front/main.less'
				},
				options: {
					compress: true,
					sourceMap: true,
					sourceMapFilename: 'web/assets/frontend/styles/index2.css.map',
					sourceMapBasepath: 'web',
					sourceMapURL: 'index2.css.map',
					sourceMapRootpath: '../../../'
				}
			},

			wysiwyg1: {
				files: {
					'web/assets/admin/styles/wysiwyg.css': 'src/SS6/ShopBundle/Resources/styles/common/front/wysiwyg.less'
				},
				options: {
					compress: true
				}
			},
			wysiwyg2: {
				files: {
					'web/assets/admin/styles/wysiwyg.css': 'src/SS6/ShopBundle/Resources/styles/domain2/front/wysiwyg.less'
				},
				options: {
					compress: true
				}
			}
		},


		legacssy: {
			admin: {
				options: {
					legacyWidth: 1200,
					matchingOnly: false,
					overridesOnly: false
				},
				files: {
						'web/assets/admin/styles/index-ie8.css': 'web/assets/admin/styles/index.css'
				}
			},

			frontend1: {
				options: {
					legacyWidth: 1200,
					matchingOnly: false,
					overridesOnly: false
				},
				files: {
					'web/assets/frontend/styles/index1-ie8.css': 'web/assets/frontend/styles/index1.css'
				}
			},
			frontend2: {
				options: {
					legacyWidth: 1200,
					matchingOnly: false,
					overridesOnly: false
				},
				files: {
					'web/assets/frontend/styles/index2-ie8.css': 'web/assets/frontend/styles/index2.css'
				}
			}
		},

		sprite: {
			admin: {
				src: 'web/assets/admin/images/icons/*.png',
				dest: 'web/assets/admin/images/icons/*.png',
				destImg: 'web/assets/admin/images/sprites/sprite.png',
				destCSS: 'src/SS6/ShopBundle/Resources/styles/common/admin/libs/sprites.less',
				imgPath: '../images/sprites/sprite.png',
				algorithm: 'binary-tree',
				padding: 50,
				engine: 'pngsmith',
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
				destCSS: 'src/SS6/ShopBundle/Resources/styles/common/front/libs/sprites.less',
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

		styledocco: {
			dist: {
				options: {
					name: 'Shopsys 6 - common',
					include: [
						'web/assets/frontend/styles/index1.css',
						'web/assets/admin/styles/index.css'
					]
				},
				files: {
					'docs/common/frontend/components': 'src/SS6/ShopBundle/Resources/styles/common/front/components/',
					'docs/common/frontend/core/buttons': 'src/SS6/ShopBundle/Resources/styles/common/front/core/forms/buttons.less',
					'docs/common/admin/components': 'src/SS6/ShopBundle/Resources/styles/common/admin/components/'
				}
			}
		},

		watch: {
			admin: {
				files: ['src/SS6/ShopBundle/Resources/styles/common/admin/**/*.less', 'web/assets/admin/images/icons/**/*.png'],
				tasks: ['admin'],
				options: {
					livereload: true,
				}
			},
			frontendSprite: {
				files: ['web/assets/frontend/images/icons/**/*.png'],
				tasks: ['frontendSprite'],
				options: {
					livereload: true,
				}
			},
			frontend1: {
				files: ['src/SS6/ShopBundle/Resources/styles/common/front/**/*.less'],
				tasks: ['frontend1'],
				options: {
					livereload: true,
				}
			},
			frontend2: {
				files: ['src/SS6/ShopBundle/Resources/styles/domain2/front/**/*.less'],
				tasks: ['frontend2'],
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
	grunt.loadNpmTasks('grunt-styledocco');

	grunt.registerTask('default', ['sprite', 'less', 'legacssy']);

	grunt.registerTask('frontend1', ['less:frontend1', 'legacssy:frontend1', 'less:wysiwyg1']);
	grunt.registerTask('frontend2', ['less:frontend2', 'legacssy:frontend2', 'less:wysiwyg2']);
	grunt.registerTask('frontendSprite', ['sprite:frontend']);
	grunt.registerTask('admin', ['sprite:admin','less:admin', 'legacssy:admin' ]);

	grunt.registerTask('docs', ['styledocco']);
};
