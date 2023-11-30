<?php $__env->startSection('title', 'API DOCS'); ?>


<?php $__env->startSection('content'); ?>
    <div class="row mt-4">
        <!-- Navigation -->
        <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
            <div class="d-flex justify-content-between flex-column mb-2 mb-md-0">
                <ul class="nav nav-align-left nav-pills flex-column">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-webhook">
                            <i class="ti ti-webhook me-1 ti-sm"></i>
                            <span class="align-middle fw-semibold">Example WebHook</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sendtext">
                            <i class="ti ti-brand-whatsapp me-1 ti-sm"></i>
                            <span class="align-middle fw-semibold">Api Send Text</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-sendmedia">
                            <i class="ti ti-brand-whatsapp me-1 ti-sm"></i>
                            <span class="align-middle fw-semibold">Api Send Media</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-sendbutton">
                            <i class="ti ti-brand-whatsapp me-1 ti-sm"></i>
                            <span class="align-middle fw-semibold">Api Send Button</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-sendlistbutton">
                            <i class="ti ti-brand-whatsapp me-1 ti-sm"></i>
                            <span class="align-middle fw-semibold">Api Send List Button</span>
                        </button>
                    </li>
                </ul>
                <div class="d-none d-md-block">
                    <div class="mt-4">
                        <img src="<?php echo asseT('assets/img/illustrations/girl-sitting-with-laptop.png'); ?>" class="img-fluid" width="270" alt="FAQ Image" />
                    </div>
                </div>
            </div>
        </div>
        <!-- /Navigation -->

        <!-- FAQ's -->
        <div class="col-lg-9 col-md-8 col-12">
            <div class="tab-content py-0">
                <div class="tab-pane fade show active" id="tab-webhook" role="tabpanel">
                    <div class="d-flex mb-3 gap-3">
                        <div>
                            <span class="badge bg-label-primary rounded-2 p-2">
                                <i class="ti ti-webhook ti-lg"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-0">
                                <span class="align-middle">Webhook</span>
                            </h4>
                            <small>Make Webhook Like AutoResponder</small>
                        </div>
                    </div>
                    <div id="accordionPayment" class="accordion">
                        <div class="card">
                            <div class="card-body">
                                Webhooks are configured to send push notifications to a specified URL whenever incoming message activity occurs.
                                <div class="card accordion-item mt-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionPayment-1" aria-controls="accordionPayment-1">
                                            CODE EXAMPLE PHP
                                        </button>
                                    </h2>

                                    <div id="accordionPayment-1" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <pre style="border-radius: 6px;"><code class="hljs language-php"><?php echo $webhook; ?></code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane fade " id="tab-sendtext" role="tabpanel">
                    <?php echo $__env->make('api.sendtext', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="tab-pane fade" id="tab-sendmedia" role="tabpanel">
                    <?php echo $__env->make('api.sendmedia', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="tab-pane fade " id="tab-sendbutton" role="tabpanel">
                    <?php echo $__env->make('api.sendbutton', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="tab-pane fade " id="tab-sendlistbutton" role="tabpanel">
                    <?php echo $__env->make('api.sendlistbutton', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
        <!-- /FAQ's -->
    </div>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="<?php echo asset('assets/highlight/styles/github-dark-dimmed.min.css'); ?>">
    <script src="<?php echo asset('assets/highlight/highlight.min.js'); ?>"></script>
    <style>
        .hljs {
            background: #0b1120;
            border: 1px solid #1e293b;
            border-radius: 5px
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('dash.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\WHATSAPP-AUTO-SENDER\core\resources\views/api/apidocs.blade.php ENDPATH**/ ?>