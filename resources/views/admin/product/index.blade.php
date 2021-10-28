@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/product/product.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Products</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        @can('add product')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a href="{{ route('admin.product.create') }}" class="btn btn-success">
                                        Add New <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endcan
                        <table class="table table-bordered table-striped" id="products-table"
                            @if(auth()->user()->can('edit product') || auth()->user()->can('delete product'))
                                @if (auth()->user()->can('edit product') && auth()->user()->can('delete product'))
                                    data-url="{{ route('admin.product.data', ['permission' => 1]) }}"
                                @elseif(auth()->user()->can('edit product'))
                                    data-url="{{ route('admin.product.data', ['permission' => 2]) }}"
                                @elseif(auth()->user()->can('delete product')) 
                                    data-url="{{ route('admin.product.data', ['permission' => 3]) }}"
                                @endif 
                            @else
                                data-url="{{ route('admin.product.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th class="text-center">Status</th>
                                    @if(auth()->user()->can('edit product') || auth()->user()->can('delete product'))
                                        <th class="text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </section>
@endsection
