<!DOCTYPE html>
<html>
<head>
    <title>New Flower Request</title>
</head>
<body>
    <h1>New Flower Request Received</h1>
    <p><strong>Request ID:</strong> {{ $flowerRequest->request_id }}</p>
    <p><strong>User:</strong> {{ $flowerRequest->user->mobile_number }} </p>
    <p>
       
            <strong>Address:</strong> {{ $flowerRequest->address->apartment_flat_plot ?? "" }}, {{ $flowerRequest->address->localityDetails->locality_name ?? "" }}<br>
            <strong>Landmark:</strong> {{ $flowerRequest->address->landmark ?? "" }}<br>

            <strong>City:</strong> {{ $flowerRequest->address->city ?? ""}}<br>
            <strong>State:</strong> {{ $flowerRequest->address->state ?? ""}}<br>
            <strong>Pin Code:</strong> {{ $flowerRequest->address->pincode ?? "" }}
       
    </p>
    <p><strong>Description:</strong> {{ $flowerRequest->description }}</p>
    <p><strong>Suggestion:</strong> {{ $flowerRequest->suggestion }}</p>
    <p><strong>Date:</strong> {{ $flowerRequest->date }}</p>
    <p><strong>Time:</strong> {{ $flowerRequest->time }}</p>
    <h3>Flower Items:</h3>
    <ul>
        @foreach ($flowerRequest->flowerRequestItems as $item)
            <li>{{ $item->flower_name }} - {{ $item->flower_quantity }} {{ $item->flower_unit }}</li>
        @endforeach
    </ul>
</body>
</html>
