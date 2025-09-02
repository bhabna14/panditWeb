@if(empty($rows))
  <div class="text-muted">No data.</div>
@else
  <div class="table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead>
        <tr>
          <th style="width:36px;">#</th>
          <th>Flower</th>
          <th>Unit</th>
          <th class="text-end">Est Qty</th>
          <th class="text-end">Act Qty</th>
          <th class="text-end">Δ Qty</th>
          <th class="text-end">Est Value</th>
          <th class="text-end">Act Value</th>
          <th class="text-end">Δ Value</th>
        </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($rows as $r)
          <tr>
            <td>{{ $i++ }}</td>
            <td>{{ $r['flower_name'] }}</td>
            <td><span class="chip">{{ $r['unit'] }}</span></td>
            <td class="text-end amount">{{ number_format($r['est_qty'],2) }}</td>
            <td class="text-end amount">{{ number_format($r['act_qty'],2) }}</td>
            <td class="text-end amount {{ $r['diff_qty']>=0?'pos':'neg' }}">{{ number_format($r['diff_qty'],2) }}</td>
            <td class="text-end amount">₹ {{ number_format($r['est_value'],2) }}</td>
            <td class="text-end amount">₹ {{ number_format($r['act_value'],2) }}</td>
            <td class="text-end amount {{ $r['diff_value']>=0?'pos':'neg' }}">₹ {{ number_format($r['diff_value'],2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
