module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				sourceMap: true,
				sourceMapIncludeSources: true
			},
			widget: {
				options: {
					sourceMapUrl: '/js/dist/policat_widget.js.map'
				},
				files: {
					'web/js/dist/policat_widget.js': ['bower_components/jscolor/jscolor.js', 'bower_components/select2/select2.js', 'web/js/policat_widget.js'],
				}
			},
			widget2: {
				files: {
					'web/js/dist/policat_widget_outer.js': ['web/js/policat_widget_outer.js'],
					'web/js/dist/select2.js' : ['bower_components/select2/select2.js'],
					'web/js/dist/jscolor.js': ['bower_components/jscolor/jscolor.js'],
					'web/js/dist/signers.js': ['web/js/signers.js']
				}
			},
			frontend: {
				files: {
					'web/js/dist/frontend.js': [
						'bower_components/jquery-1.7/jquery.js',
						'bower_components/bootstrap-2.0/js/bootstrap-modal.js',
						'bower_components/bootstrap-2.0/js/bootstrap-alert.js',
						'bower_components/bootstrap-2.0/js/bootstrap-button.js',
						'bower_components/bootstrap-2.0/js/bootstrap-tab.js',
						'bower_components/bootstrap-2.0/js/bootstrap-dropdown.js',
						'bower_components/bootstrap-2.0/js/bootstrap-collapse.js',
						'bower_components/bootstrap-2.0/js/bootstrap-tooltip.js',
						'bower_components/bootstrap-2.0/js/bootstrap-popover.js',
						'bower_components/jquery-elastic/jquery.elastic.source.js',
						'web/js/dashboard.js'
					],
					'web/js/dist/jquery.highlighttextarea.js': [
						'bower_components/jquery-highlightTextarea/jquery.highlighttextarea.js'
					],
					'web/js/dist/jquery.iframe-transport.js': [
						'bower_components/jquery-iframe-transport/jquery.iframe-transport.js'
					]
				}
			}
		},
		less: {
			options: {
				sourceMap: true,
				compress: true,
				outputSourceFiles: true,
				sourceMapBasepath: 'web',
				sourceMapRootpath: '/'
			},
			frontend: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend.less.map'
				},
				files: {
					'web/css/dist/frontend.css' : 'web/css/frontend.less'
				}
			},
			frontend_print: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend_print.less.map'
				},
				files: {
					'web/css/dist/frontend_print.css' : 'web/css/frontend_print.less'
				}
			},
			frontend4: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend4.less.map'
				},
				files: {
					'web/css/dist/frontend4.css' : 'web/css/frontend4.less'
				}
			},
			widget: {
				options: {
					sourceMapFilename: 'web/css/dist/policat_widget.less.map',
					rootpath: '/css/dist/'
				},
				files: {
					'web/css/dist/policat_widget.css': [
						'web/css/policat_widget.less',
						'bower_components/select2/select2.css',
						'bower_components/select2/select2-bootstrap.css',
					]
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
						'bower_components/select2/select2.css',
						'bower_components/select2/select2-bootstrap.css'
					],
					'web/css/dist/jquery.highlighttextarea.css': [
						'bower_components/jquery-highlightTextarea/jquery.highlighttextarea.css'
					],
					'web/css/dist/bootstrap4.min.css': [
						'web/css/dist/bootstrap4.css'
					]
				}
			}
		},
		copy: {
			select2: {
				expand:true,
				cwd: 'bower_components/select2/',
				src: ['*.png', '*.gif'],
				dest: 'web/css/dist/'
			},
			html5shiv: {
				src: ['bower_components/html5shiv/dist/html5shiv.min.js'],
				dest: 'web/js/dist/html5shiv.js'
			},
			jscolor: {
				expand:true,
				cwd: 'bower_components/jscolor/',
				src: ['*.png', '*.gif'],
				dest: 'web/js/dist/'
			},
			jquery: {
				src: 'bower_components/jquery-1.10/jquery.min.js',
				dest: 'web/js/static/jquery-1.10.2.min.js'
			},
			chosen_js: {
				expand:true,
				cwd: 'bower_components/chosen/',
				src: ['*.js'],
				dest: 'web/js/dist/'
			},
			chosen_css: {
				expand:true,
				cwd: 'bower_components/chosen/',
				src: ['*.png', '*.css'],
				dest: 'web/css/dist/'
			},
			showdown: {
				src: ['bower_components/showdown/dist/showdown.min.js'],
				dest: 'web/js/dist/showdown.js'
			},
			markitup_js: {
				expand:true,
				cwd: 'bower_components/markitup/markitup/',
				src: ['**/*.js', '**/*.html'],
				dest: 'web/js/dist/markitup'
			},
			markitup_css: {
				expand:true,
				cwd: 'bower_components/markitup/markitup/',
				src: ['**/*.css', '**/*.png'],
				dest: 'web/css/dist/markitup'
			},
			bootstrap_icon: {
				expand:true,
				cwd: 'bower_components/bootstrap-2.0/img/',
				src: ['*.png'],
				dest: 'web/css/dist/img'
			},
			bootstrap4_js: {
				src: ['bower_components/bootstrap/dist/js/bootstrap.min.js'],
				dest: 'web/js/dist/bootstrap4.min.js'
			},
			tether_js: {
				src: ['bower_components/tether/dist/js/tether.min.js'],
				dest: 'web/js/dist/tether.min.js'
			},
			jquery31_js: {
				src: ['bower_components/jquery-3.1/dist/jquery.min.js'],
				dest: 'web/js/dist/jquery-3.1.min.js'
			}
		},
		exec: {
			"sass-bootstrap": {
				command: "node_modules/.bin/node-sass --output-style expanded --source-map true --precision 6 bower_components/bootstrap/scss/bootstrap.scss web/css/dist/bootstrap4.css",
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
