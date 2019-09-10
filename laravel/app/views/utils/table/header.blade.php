<th data-column="{{{ $id }}}">
    {{{ $name }}}
    @if ( $id == Input::get('sf') || ( isset($default) && Input::get('sf') == '' ) )
    <i class="fa fa-caret-{{ Input::get('dir') == 'desc' ? 'down' : 'up' }}"></i>
    @endif
</th>