module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			widget: {
				options: {
					sourceMap: true
				},
				files: {
					'web/js/dist/policat_widget.js': ['web/js/policat_widget.js'],
					'web/js/dist/policat_widget_outer.js': ['web/js/policat_widget_outer.js'],
					'web/js/dist/select2.js' : ['bower_components/select2/select2.js'],
					'web/js/dist/jscolor.js': [
						'bower_components/jscolor/jscolor.js'
					],
					'web/js/dist/jscrollpane.js': [
						'bower_components/jscrollpane/script/jquery.jscrollpane.js',
						'bower_components/jscrollpane/script/jquery.mousewheel.js'
					]
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
					sourceMapFilename: 'web/css/dist/frontend.less.map',
				},
				files: {
					'web/css/dist/frontend.css' : 'web/css/frontend.less',
				}
			},
			frontend_print: {
				options: {
					sourceMapFilename: 'web/css/dist/frontend_print.less.map',
				},
				files: {
					'web/css/dist/frontend_print.css' : 'web/css/frontend_print.less',
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
			widget: {
				files: {
					'web/css/dist/policat_widget.css': ['web/css/policat_widget.css'],
					'web/css/dist/jscrollpane.css': ['bower_components/jscrollpane/style/jquery.jscrollpane.css']
				}
			},
			frontend: {
				files: {
					'web/css/dist/select2.css': [
						'bower_components/select2/select2.css',
						'bower_components/select2/select2-bootstrap.css'
					],
					'web/css/dist/jquery.highlighttextarea.css': [
						'bower_components/jquery-highlightTextarea/jquery.highlighttextarea.css'
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
				src: ['bower_components/showdown/compressed/showdown.min.js'],
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
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.registerTask('default', ['uglify', 'less', 'cssmin', 'copy']);
};