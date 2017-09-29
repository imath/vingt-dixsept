/* jshint node:true */
/* global module */
module.exports = function( grunt ) {
	require( 'matchdep' ).filterDev( ['grunt-*', '!grunt-legacy-util'] ).forEach( grunt.loadNpmTasks );
	grunt.util = require( 'grunt-legacy-util' );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		jshint: {
			options: grunt.file.readJSON( '.jshintrc' ),
			grunt: {
				src: ['Gruntfile.js']
			},
			all: ['Gruntfile.js', 'js/*.js']
		},
		checktextdomain: {
			options: {
				correct_domain: false,
				text_domain: ['vingt-dixsept','default'],
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'_n:1,2,4d',
					'_ex:1,2c,3d',
					'_nx:1,2,4c,5d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: ['**/*.php', '!**/node_modules/**'],
				expand: true
			}
		},
		clean: {
			all: ['assets/*.min.css', 'assets/*.min.js']
		},
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: ['/node_modules'],
					mainFile: 'style.css',
					potFilename: 'vingt-dixsept.pot',
					processPot: function( pot ) {
						pot.headers['last-translator']      = 'imath <contact@imathi.eu>';
						pot.headers['language-team']        = 'FRENCH <contact@imathi.eu>';
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/imath/vingt-dixsept/issues';
						return pot;
					},
					type: 'wp-theme'
				}
			}
		},
		uglify: {
			minify: {
				extDot: 'last',
				expand: true,
				ext: '.min.js',
				src: ['assets/*.js', '!*.min.js']
			},
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
				'<%= grunt.template.today("UTC:yyyy-mm-dd h:MM:ss TT Z") %> - ' +
				'https://imathi.eu/tag/vingt-dixsept */\n'
			}
		},
		cssmin: {
			minify: {
				extDot: 'last',
				expand: true,
				ext: '.min.css',
				src: ['assets/*.css', '!*.min.css']
			}
		},
		imagemin: {
			core: {
				expand: true,
				cwd: ['assets/images/'],
				src: ['**/*.{gif,jpg,jpeg,png}'],
				dest: ['assets/images/']
			}
		},
		jsvalidate:{
			src: ['assets/*.js'],
			options:{
				globals: {},
				esprimaOptions:{},
				verbose: false
			}
		},
		'git-archive': {
			archive: {
				options: {
					'format'  : 'zip',
					'output'  : '<%= pkg.name %>.zip',
					'tree-ish': 'HEAD@{0}'
				}
			}
		}
	} );

	/** Tasks. ******************************************************************/

	grunt.registerTask( 'jstest', ['jsvalidate', 'jshint'] );

	grunt.registerTask( 'shrink', ['cssmin', 'uglify', 'imagemin'] );

	grunt.registerTask( 'compress', ['git-archive'] );

	grunt.registerTask( 'release', ['checktextdomain', 'makepot', 'clean', 'jstest', 'shrink', 'compress'] );

	// Default task.
	grunt.registerTask( 'default', ['checktextdomain'] );
};
