module.exports = function(grunt) {
    grunt.initConfig({
        less: {
            dev: {
                options: {
                    dumpLineNumbers: 'all'
                },
                files: {
                  "packages/css/style.css": "packages/less/style.less"
                }
            },
            dist: {// Another target
                options: {
                    compress: true
                },
                files: {
                  "packages/css/style.css": "packages/less/style.less"
                }
            }
        },
        uglify: {
            dist: {
                options: {
                    wrap: false,
                    mangle: false
                },
                files: {
                    'packages/js/app.min.js': [
                        'packages/js/lib/jquery.js',
                        'packages/js/lib/bootstrap.js',
                        'packages/js/lib/jquery.liveFilter.js',
                        'packages/js/lib/jquery.blockUI.js',
                        'packages/js/lib/jquery.form.js',
                        'packages/js/lib/bootstrapValidator.js',
                        'packages/js/lib/pnotify.custom.js',
                        'packages/js/lib/handlebars.js',
                        'packages/js/lib/rails.js',
                        'packages/js/lib/jquery.parseparams.js',
                        'packages/js/lib/typeahead.bundle.js',
                        'packages/js/lib/jquery.tabbable.js',
                        'packages/js/lib/bootstrap-datepicker.js',
                        'packages/js/app.js'
                    ]
                }
            },
            dev: {
                options: {
                    wrap: false,
                    mangle: false,
                    beautify: true
                },
                files: {
                }
            }
        },
        watch: {
            js: {
                files: ['packages/js/app.js', 'packages/js/lib/**/*.js'],
                tasks: ['uglify'],
                options: {
                    //nospawn: true,
                }
            },
            less: {
                files: ['packages/less/**/*.less'],
                tasks: ['less:dev'],
                options: {
                    //nospawn: true,
                }
            },
            css: {
                files: ['packages/css/**/*.css'],
                tasks: ['cssmin'],
                options: {
                    //nospawn: true,
                }
            }
        },
        cssmin: {
            combine: {
                files: {
                    //'css/app.css': ['public_html/css/files/ratchet.css', 'public_html/css/files/pageslider.css', 'public_html/css/files/styles.css', 'public_html/css/files/leaflet.css', 'public_html/css/screen.css']
                }
            }
        }
    });

    
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['less:dist', 'cssmin', 'uglify:dist']);
    grunt.registerTask('dev', ['less:dev', 'cssmin', 'uglify:dev']);
};
