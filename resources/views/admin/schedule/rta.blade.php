@extends('admin.default')

@section('content')
<!-- ### $App Screen Content ### --> 
   <rta-sched-section v-bind:user-profile="{{json_encode($pageOnload->id)}}"></rta-sched-section>

@endsection
