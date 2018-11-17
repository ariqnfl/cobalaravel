@extends('layouts.global')
@section('title') Trashed Categories @endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <form action="{{route('categories.index')}}">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Filter by Category Name"
                           value="{{Request::get('name')}}" name="name">
                    <div class="input-group-append">
                        <input type="submit" value="Filter" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('categories.index')}}">Published</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-primary" href="{{route('categories.trash')}}">Trash</a>
                </li>
            </ul>
        </div>
    </div>
    <hr class="my-3">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{$category->name}}</td>
                        <td>{{$category->slug}}</td>
                        <td>
                            @if($category->image)
                                <img src="{{asset('storage/'.$category->image)}}" width="48px">
                            @endif
                        </td>
                        <td>
                            <a href="{{route('categories.restore',['id'=> $category ->id])}}" class="btn btn-success btn-sm">Restore</a>
                            <form class="d-inline" action="{{route('categories.delete-permanent',['id'=>$category->id])}}" method="POST" onsubmit="return confirm('Delete this category permanently?')">
                                @csrf
                                @method('delete')
                                <input type="submit" class="btn btn-danger btn-sm" value="DELETE">
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="10">
                        {{$categories->appends(Request::all())->links()}}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection