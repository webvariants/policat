module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			widget: {
				options: {
					sourceMap: true
				},
				files: {
					'web/js/dist/policat_widget.js': ['node_modules/jscolor-picker/jscolor.js', 'web/js/policat_widget.js'],
					'web/js/dist/policat_widget_outer.js': ['web/js/policat_widget_outer.js'],
					'web/js/dist/select2.js' : ['node_modules/select2/select2.js'],
					'web/js/dist/jscolor.js': ['node_modules/jscolor-picker/jscolor.js'],
					'web/js/dist/signers.js': ['web/js/signers.js'],
					'web/js/dist/chosen.jquery.min.js': ['node_modules/chosen-js/chosen.jquery.js']
				}
			},
			frontend: {
				options: {
					sourceMap: true
				},
				files: {
					'web/js/dist/frontend.js': [
						'node_modules/jquery-elastic/jquery.elastic.source.js',
						'web/js/dashboard.js'
					],
					'web/js/dist/jquery.highlighttextarea.js': [
						'node_modules/jquery-highlightTextarea/jquery.highlighttextarea.js'
					],
					'web/js/dist/jquery.iframe-transport.js': [
						'node_modules/jquery.iframe-transport/jquery.iframe-transport.js'
					]
				}
			}
		},
		less: {
			options: {
				sourceMap: true,
				compress: true,
				sourceMapBasepath: 'web',
				sourceMapRootpath: '/'
			},
			frontend_print: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend_print.less.map'
				},
				files: {
					'web/css/dist/frontend_print.css' : 'web/css/frontend_print.less'
				}
			},
			frontend: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend.less.map'
				},
				files: {
					'web/css/dist/frontend4.css' : 'web/css/frontend.less'
				}
			},
			widget: {
				options: {
					sourceMapFilename: 'web/css/dist/policat_widget.less.map'
				},
				files: {
					'web/css/dist/policat_widget.css': 'web/css/policat_widget.less'
				}
			},
			widget_variables: {
				options: {
					sourceMapFilename: 'web/css/dist/policat_widget_variables.less.map'
				},
				files: {
					'web/css/dist/policat_widget_variables.css': 'web/css/policat_widget_variables.less'
				}
			},
			theme_sleek: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/sleek.less.map'
				},
				files: {
					'web/css/dist/theme/sleek.css': 'web/css/theme/sleek.less'
				}
			},
			theme_minimal: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/minimal.less.map'
				},
				files: {
					'web/css/dist/theme/minimal.css': 'web/css/theme/minimal.less'
			  }
			},
			theme_light: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/light.less.map'
				},
				files: {
					'web/css/dist/theme/light.css': 'web/css/theme/light.less'
				}
			},
			bootstrap_custom: {
				options: {
					sourceMapFilename: 'web/css/dist/bootstrap-custom.less.map'
				},
				files: {
					'web/css/dist/bootstrap-custom.css': 'web/css/bootstrap-custom.less'
				}
			},
			theme_classic_modified: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/classic-modified.less.map'
				},
				files: {
					'web/css/dist/theme/classic-modified.css': 'web/css/theme/classic-modified.less'
				}
			},
			theme_flat: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/flat.less.map'
				},
				files: {
					'web/css/dist/theme/flat.css': 'web/css/theme/flat.less'
			  }
			},
			theme_sleek_variant_1: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/sleek-variant-1.less.map'
				},
				files: {
					'web/css/dist/theme/sleek-variant-1.css': 'web/css/theme/sleek-variant-1.less'
			  }
			}
		},
		cssmin: {
			options: {
				advanced: false,
				aggressiveMerging: false,
				mediaMerging: false,
				shorthandCompacting: false,
				roundingPrecision: -1,
				sourceMap: true
			},
			frontend: {
				files: {
					'web/css/dist/select2.css': [
						'node_modules/select2/select2.css',
						'node_modules/select2/select2-bootstrap.css'
					],
					'web/css/dist/jquery.highlighttextarea.css': [
						'node_modules/jquery-highlightTextarea/jquery.highlighttextarea.css'
					],
					'web/css/dist/bootstrap4.min.css': [
						'web/css/dist/bootstrap4.css'
					],
					'web/css/dist/chosen.min.css': [
						'node_modules/chosen-js/chosen.css'
					]
				}
			}
		},
		copy: {
			select2: {
				expand:true,
				cwd: 'node_modules/select2/',
				src: ['*.png', '*.gif'],
				dest: 'web/css/dist/'
			},
			// html5shiv: {
			// 	src: ['bower_components/html5shiv/dist/html5shiv.min.js'],
			// 	dest: 'web/js/dist/html5shiv.js'
			// },
			// jscolor: {
			// 	expand:true,
			// 	cwd: 'bower_components/jscolor/',
			// 	src: ['*.png', '*.gif'],
			// 	dest: 'web/js/dist/'
			// },
			jquery: {
				src: 'node_modules/jquery-1.10/jquery.min.js',
				dest: 'web/js/dist/jquery-1.10.2.min.js'
			},
			chosen_sprite: {
				expand:true,
				cwd: 'node_modules/chosen-js/',
				src: ['*.png'],
				dest: 'web/css/dist/'
			},
			showdown: {
				src: ['node_modules/showdown/dist/showdown.min.js'],
				dest: 'web/js/dist/showdown.js'
			},
			markitup_js: {
				expand:true,
				cwd: 'node_modules/markItUp!/markitup/',
				src: ['**/*.js', '**/*.html'],
				dest: 'web/js/dist/markitup'
			},
			markitup_css: {
				expand:true,
				cwd: 'node_modules/markItUp!/markitup/',
				src: ['**/*.css', '**/*.png'],
				dest: 'web/css/dist/markitup'
			},
			bootstrap4_js: {
				src: ['node_modules/bootstrap/dist/js/bootstrap.min.js'],
				dest: 'web/js/dist/bootstrap4.min.js'
			},
			tether_js: {
				src: ['node_modules/tether/dist/js/tether.min.js'],
				dest: 'web/js/dist/tether.min.js'
			},
			popper_js: {
				src: ['node_modules/popper.js/dist/umd/popper.min.js'],
				dest: 'web/js/dist/popper.min.js'
			},
			jquery31_js: {
				src: ['node_modules/jquery/dist/jquery.min.js'],
				dest: 'web/js/dist/jquery-3.1.min.js'
			}
		},
		exec: {
			"sass-bootstrap": {
				command: "node_modules/.bin/node-sass --output-style expanded --source-map true --precision 6 web/css/bootstrap.sass web/css/dist/bootstrap4.css"
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-exec');
	grunt.registerTask('sass', ['exec:sass-bootstrap']);
	grunt.registerTask('default', ['uglify', 'less', 'sass', 'cssmin', 'copy']);
};
