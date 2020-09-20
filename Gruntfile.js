module.exports = function (grunt) {
    'use strict';

    require('jit-grunt')(grunt);

    // Arrange configs alphabetically
    grunt.initConfig({
        clean: {
            vendor: {
                src: ['public/vendor'],
            },
        },
        copy: {
            // 'assets': {
            //     files: [
            //         {
            //             cwd: 'assets/css',
            //             src: '**/*',
            //             dest: 'public/style/css/custom',
            //             expand: true
            //         },
            //         {
            //             cwd: 'assets/js',
            //             src: '**/*',
            //             dest: 'public/js/custom',
            //             expand: true
            //         }
            //     ]
            // },
            'bootstrap': {
                files: [
                    {
                        src: 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
                        dest: 'public/vendor/bootstrap/js/bootstrap.bundle.min.js'
                    },
                    {
                        src: 'node_modules/bootstrap/dist/css/bootstrap.min.css',
                        dest: 'public/vendor/bootstrap/css/bootstrap.min.css'
                    }
                ]
            },
            'jquery': {
                files: [
                    {
                        src: 'node_modules/jquery/dist/jquery.min.js',
                        dest: 'public/vendor/jquery/jquery.min.js'
                    }
                ]
            },
            'jquery-ui': {
                files: [
                    {
                        src: 'node_modules/jquery-ui.1.11.1/dist/jquery-ui.css',
                        dest: 'public/vendor/jquery-ui/jquery-ui.css'
                    },
                    {
                        src: 'node_modules/jquery-ui.1.11.1/dist/jquery-ui.js',
                        dest: 'public/vendor/jquery-ui/jquery-ui.js'
                    },
                    {
                        cwd: "node_modules/jquery-ui.1.11.1/themes/base/images",
                        src: '**/*',
                        dest: 'public/vendor/jquery-ui/images',
                        expand: true
                    }
                ]
            },
            'jquery-easing': {
                files: [
                    {
                        src: 'node_modules/jquery.easing/jquery.easing.min.js',
                        dest: 'public/vendor/jquery-easing/jquery.easing.min.js'
                    }
                ]
            },
            'tagsinput-revisited': {
                files: [
                    {
                        src: 'node_modules/jquery.tagsinput-revisited/src/jquery.tagsinput-revisited.js',
                        dest: 'public/vendor/tagsinput-revisited/jquery.tagsinput-revisited.js'
                    },
                    {
                        src: 'node_modules/jquery.tagsinput-revisited/src/jquery.tagsinput-revisited.css',
                        dest: 'public/vendor/tagsinput-revisited/jquery.tagsinput-revisited.css'
                    }
                ]
            },
            'fontawesome': {
                files: [
                    {
                        src: 'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
                        dest: 'public/vendor/fontawesome-free/css/all.min.css'
                    },
                    {
                        cwd: "node_modules/@fortawesome/fontawesome-free/webfonts",
                        src: '**/*',
                        dest: 'public/vendor/fontawesome-free/webfonts',
                        expand: true
                    }
                ]
            },
            'chartjs': {
                files: [
                    {
                        src: 'node_modules/chart.js/dist/Chart.min.js',
                        dest: 'public/vendor/chart.js/Chart.min.js'
                    }
                ]
            },
            'bs-custom-file-input': {
                files: [
                    {
                        src: 'node_modules/bs-custom-file-input/dist/bs-custom-file-input.min.js',
                        dest: 'public/vendor/bs-custom-file-input/bs-custom-file-input.min.js'
                    }
                ]
            },
            'datatables': {
                files: [
                    {
                        src: 'node_modules/datatables.net/js/jquery.dataTables.js',
                        dest: 'public/vendor/datatables/jquery.dataTables.js'
                    },
                    {
                        src: 'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
                        dest: 'public/vendor/datatables/dataTables.bootstrap4.css'
                    },
                    {
                        src: 'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js',
                        dest: 'public/vendor/datatables/dataTables.bootstrap4.js'
                    }
                ]
            }
        }
    });

    //#################### BEGIN TASKS REGISTER ####################

    grunt.registerTask('default', [
        'clean:vendor',
        'copy:bootstrap',
        'copy:jquery',
        'copy:jquery-ui',
        'copy:jquery-easing',
        'copy:tagsinput-revisited',
        'copy:fontawesome',
        'copy:chartjs',
        'copy:bs-custom-file-input',
        'copy:datatables',
    ]);

    //#################### END TASKS REGISTER ####################
};
