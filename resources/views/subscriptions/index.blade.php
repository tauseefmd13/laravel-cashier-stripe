@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('All Subscription') }}
                    
                    <div class="col-xs-12 col-sm-12 col-md-12 text-right" style="margin-top:-25px;">
						<a class="btn btn-sm btn-primary pull-right" href="{{ route('subscriptions.create') }}"> Create Subscription</a>
					</div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <table class="table table-bordered">
                        <tr>
				            <th>S.No.</th>
				            <th>Amount</th>
				            <th>Interval</th>
				            <th>End Date</th>
				            <th>Action</th>
				        </tr>
                        @foreach ($subscriptions as $key => $subscription)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>${{ number_format($subscription->plan->amount/100,2) }}</td>
                            <td>{{ $subscription->plan->interval_count .' '. $subscription->plan->interval }}</td>
                            <td>{{ date('Y-m-d H:i:s',$subscription->current_period_end) }}</td>
                            <td>
                                <a class="btn btn-sm btn-warning" href="{{ route('subscriptions.show',$subscription->id) }}">View</a>

                                @if(empty($subscription->canceled_at))
                                    <form action="{{ route('subscriptions.cancel',$subscription->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to cancel ?');">Cancel</button>
                                    </form>
                                @else
                                    <form action="{{ route('subscriptions.resume',$subscription->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure to resume ?');">Resume</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection