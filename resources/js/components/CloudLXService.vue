<template>
    <div id="cloudLXServices">
        <div class="loader" v-if="loadingImage">
            <div class="processing">
                <i class="fa fa-spinner fa-pulse fs80 mb10"></i><br>
                <p class="text-muted fs16">Loading results<br>Please wait</p>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead class="text-center">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Port</th>
                <th>Speed</th>
                <th>Status</th>
                <th>Protection</th>
                <th>Pricing Model</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody class="text-center">
            <tr role="button" @click="showServiceDetails(service.id)" v-for="service in services">
                <td class="fw-bold">{{ service.name }}</td>
                <td class="bg-success rounded-pill text-white">{{ service.type }}</td>
                <td class="bg-dark rounded-pill text-white">{{ service.port.name }}</td>
                <td class="bg-secondary rounded-pill text-white">{{ service.bandwidth }}</td>
                <td class="bg-primary rounded-pill text-white">{{ service.status }}</td>
                <td class="bg-danger rounded-pill text-white">{{ service.protected }}</td>
                <td class="bg-warning rounded-pill text-white">{{ service.pricing_model }}</td>
                <td class="bg-info rounded-pill text-white">{{ service.created }}</td>
            </tr>
            </tbody>
        </table>
        <div class="modal fade out" id="serviceDetailsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center"></div>
                    <div class="modal-footer d-block"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        services: Array,
        loading: true
    },
    data() {
        return {
            loadingImage: this.loading
        };
    },
    methods: {
        showServiceDetails(serviceId) {
            this.loadingImage = true;
            let serviceDetailsModalElement = document.getElementById('serviceDetailsModal');
            let serviceDetailsModal = new bootstrap.Modal(serviceDetailsModalElement, {});
            axios.get('/' + serviceId).then(
                (response) => {
                    serviceDetailsModalElement.querySelector('.modal-title').innerHTML = response.data.name;
                    let body = '<ul>';
                    body += '<li>Type: ' + response.data.type + '</li>';
                    body += '<li>Speed: ' + response.data.bandwidth + '</li>';
                    body += '<li>Status: ' + response.data.status + '</li>';
                    body += '<li>Expires on: ' + response.data.expires + '</li>';
                    body += '<li>Port Name: ' + response.data.port.name + '</li>';
                    body += '<li>Port Customer: ' + response.data.port.customer + '</li>';
                    body += '<li>Port Datacenter City: ' + response.data.port.datacentre_city + '</li>';
                    body += '<li>Port Price Currency: ' + response.data.port.price_currency + '</li>';
                    body += '</ul>';
                    serviceDetailsModalElement.querySelector('.modal-body').innerHTML = body;

                    serviceDetailsModal.show();
                }, (error) => {
                    serviceDetailsModalElement.querySelector('.modal-title').innerHTML = 'Error Status: ' + error.response.status;
                    serviceDetailsModalElement.querySelector('.modal-body').innerHTML = 'Error Message: ' + error.response.statusText;
                    serviceDetailsModal.show();
                }).finally(() => {
                    this.loadingImage = false
                });
        }
    }
}
</script>

<style scoped>
.loader {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    margin: 0;
    height: 100%;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1;
    padding-top: 20px;
    padding-bottom: 20px;
    text-align: center;
    font-size: 1.2em;
    width: 100%;
}
.processing {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.fa-spinner:before {
    content: "\f110";
    color: #95358c;
}
.mb10 {
    margin-bottom: 10px!important;
}
.fs80 {
    font-size: 80px!important;
}
.fa-pulse {
    animation: fa-spin 1s infinite steps(8);
}
.fa {
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
}
</style>
