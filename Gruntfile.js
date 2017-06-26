module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			widget: {
				options: {
					sourceMap: true
				},
				files: {
					'web/js/dist/policat_widget.js': ['bower_components/jscolor/jscolor.js', 'web/js/policat_widget.js'],
					'web/js/dist/policat_widget_outer.js': ['web/js/policat_widget_outer.js'],
					'web/js/dist/select2.js' : ['bower_components/select2/select2.js'],
					'web/js/dist/jscolor.js': ['bower_components/jscolor/jscolor.js'],
					'web/js/dist/signers.js': ['web/js/signers.js']
				}
			},
			frontend: {
				options: {
					sourceMap: true
				},
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
			bootrap_custom: {
				options: {
					sourceMapFilename: 'web/css/dist/theme/minimal.less.map'
				},
				files: {
					'web/css/dist/bootstrap-custom.css': 'web/css/bootstrap-custom.less'
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
						'web/css/dist/bootstrap4-reboot.css',
						'web/css/dist/bootstrap4.css',
						'web/css/dist/bootstrap4-grid.css'
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
			jquery31_slim_js: {
				src: ['bower_components/jquery-3.1/dist/jquery.slim.min.js'],
				dest: 'web/js/dist/jquery-3.1.slim.min.js'
			}
		},
		exec: {
			"sass-bootstrap": {
				command: "node_modules/.bin/node-sass --output-style expanded --source-map true --precision 6 bower_components/bootstrap/scss/bootstrap.scss web/css/dist/bootstrap4.css",
			},
			"sass-bootstrap-grid": {
				command: "node_modules/.bin/node-sass --output-style expanded --source-map true --precision 6 bower_components/bootstrap/scss/bootstrap-grid.scss web/css/dist/bootstrap4-grid.css"
			},
			"sass-bootstrap-reboot": {
				command: "node_modules/.bin/node-sass --output-style expanded --source-map true --precision 6 bower_components/bootstrap/scss/bootstrap-reboot.scss web/css/dist/bootstrap4-reboot.css"
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-exec');
	grunt.registerTask('sass', ['exec:sass-bootstrap', 'exec:sass-bootstrap-grid', 'exec:sass-bootstrap-reboot']);
	grunt.registerTask('default', ['uglify', 'less', 'sass', 'cssmin', 'copy']);
};
