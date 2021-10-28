@extends('admin.layout.app')

@section('js')
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/role/role.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcumb-item"><a href="{{ route('admin.role.index') }}">Roles</a></li>
                        <li class="active">{{ $role->name }}</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                <form action="{{ route('admin.role.update', ['id' => $role->id]) }}" method="post" class="row">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                autofocus value="{{ $role->name }}" />
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea name="description" rows="5"
                                class="form-control @error('description') is-invalid @enderror">{{ $role->description }}</textarea>
                            @error('description')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="top-permission">
                                <label for="checkall">
                                    <input type="checkbox" id="checkall" class="check-all" />
                                    Check All
                                </label>
                            </div>

                            @foreach ($modules as $module)
                                <div class="panel panel-info permission">
                                    <div class="panel-heading">
                                        <label for="{{ $module }}" class="text-capitalize">
                                            <input type="checkbox" id="{{ $module }}" class="check-parent" />
                                            Module {{ $module }}
                                        </label>
                                    </div>
                                    <div class="panel-body flex-between">
                                        @foreach ($permissions as $permiss)
                                            @php
                                                $endperarr = explode(' ', $permiss->name);
                                                $endper = end($endperarr);
                                            @endphp
                                            @if ($endper === $module)
                                                <div class="module-item" style="flex: 0 0 {{ 100/count(config('permission.modules.'.$module)) }}%">
                                                    <label for="{{ implode('_', $endperarr) }}" class="text-capitalize">
                                                        <input {{ collect($role->permissions()->get()->pluck('id')->toArray())->contains($permiss->id) ? 'checked' : '' }} type="checkbox" name="permission_id[]" id="{{ implode('_', $endperarr) }}" value="{{ $permiss->id }}"
                                                            class="check-child" />
                                                        {{ reset($endperarr) }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success text-uppercase" type="submit">UPDATE</button>
                        </div>
                    </div>
                </form>
            </section>
        </section>
    </section>
@endsection
