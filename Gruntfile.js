module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			widget: {
				options: {
					sourceMap: true
				},
				files: {
					'web/js/policat_widget_opt.js': ['web/js/policat_widget.js'],
					'web/js/policat_widget_outer_opt.js': ['web/js/policat_widget_outer.js'],
					'web/js/opt.js': [
						'web/js/jquery-1.7.1.min.js',
						'web/js/bootstrap-modal.js',
						'web/js/bootstrap-alert.js',
						'web/js/bootstrap-button.js',
						'web/js/bootstrap-tab.js',
						'web/js/bootstrap-dropdown.js',
						'web/js/bootstrap-collapse.js',
						'web/js/bootstrap-tooltip.js',
						'web/js/bootstrap-popover.js',
						'web/js/jquery.elastic.source.js',
						'web/js/dashboard.js'
					]
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
			target: {
				files: {
					'web/css/policat_widget_opt.css': ['web/css/policat_widget.css'],
					'web/css/opt.css': ['web/css/bootstrap.css', 'web/css/dashboard.css']
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.registerTask('default', ['uglify', 'cssmin']);
};