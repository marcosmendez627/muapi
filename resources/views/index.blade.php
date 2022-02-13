<html>
<head>
    <title>MailUp Backend Challenge</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>
<body>
    <div id="app" class="container">
        <div class="text-center">
            <h1 class="mb-4">MailUp Backend Challenge</h1>
            <button type="button" class="btn btn-primary" @click="getPhotos" v-if="showButton">Importar fotos</button>
            <div class="alert alert-info" v-else-if="processing" v-cloak>
                <i class="fa fa-spinner fa-spin mr-2"></i>Importando fotos
            </div>
            <div class="alert" :class="completeStatus == 'success' ? 'alert-success' : 'alert-danger'" v-else-if="complete" v-cloak>
                <i class="fa mr-1" :class="completeStatus == 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'"></i>@{{ completeMessage }}
            </div>
        </div>
    </div>

{{--    <script src="{{ asset('assets/js/vue.min.js') }}"></script>--}}
    <script src="{{ asset('assets/js/vue.js') }}"></script>
    <script src="{{ asset('assets/js/axios.min.js') }}"></script>
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                processing: false,
                complete: false,
                completeStatus: null,
                completeMessage: null
            },
            methods: {
                getPhotos() {
                    this.processing = true;

                    axios.get('{{ route('jpa_get_photos') }}')
                    .then(result => {
                        this.completeStatus = result.data.status;
                        this.completeMessage = result.data.message;
                    })
                    .catch(error => {
                        console.log(error);
                        this.completeStatus = 'error';
                    })
                    .then(() => {
                        this.processing = false;
                        this.complete = true;
                    });
                }
            },
            computed: {
                showButton() {
                    return !this.processing && !this.complete;
                }
            }
        });
    </script>
</body>
</html>
