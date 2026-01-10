<!-- message componet -->




@if(session()->has('success'))
    <div class="alert al alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
       
    </div>
@endif

@if(session()->has('error'))
    <div class="alert al alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        
    </div>
@endif


<script>
    setTimeout(function() {
        $('.al').alert('close');
    }, 5000);
</script>