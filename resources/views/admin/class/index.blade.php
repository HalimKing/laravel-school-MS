@extends('layouts.app')

@section('title', 'All Classes')


@section('content')
    <div class="card mb-4 p-4">
        <h6>All Classes</h6>
    </div>
    @include('includes.message')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class=" d-flex justify-content-between">
                    <h6 class="card-title">Class List</h6>
                    <a class="btn btn-primary float-end" href="{{ route('admin.classes.create') }}"> <i data-lucide="plus" class="pr-2"></i> Add Class</a>
                </div>
                <div class="table-responsive">
                  <table id="dataTableExample" class="table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->description }}</td>
                                 <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.classes.edit', $class->id) }}"><i class="w-2 h-2 ml-3" style="font-size: 4px; width: 16px; margin-right: 4px;" data-lucide="pencil"></i>Edit</a></li>
                                            <li>
                                                <form method="post" action="{{ route('admin.classes.destroy', $class->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                               
                                                <button type="submit" onclick="return confirm('You are about to delete this class permanently!')" class="dropdown-item btn text-danger"  method="delete">
                                                <i class="w-2 h-2 ml-3" style="font-size: 4px; width: 16px; margin-right: 4px; color: red" data-lucide="trash-2"></i>
                                                Delete</button>
                                                 </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if (empty($classes))
                            <tr>
                                <td>No data found</td>
                            </tr>
                        @endif
                      
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection
