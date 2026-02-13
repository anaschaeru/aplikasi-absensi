@props(['status'])

@php
  $classes = 'px-2 py-1 text-xs font-semibold rounded-full ';

  switch ($status) {
      case 'Hadir':
          $classes .= 'bg-green-100 text-green-800';
          break;
      case 'Sakit':
          $classes .= 'bg-yellow-100 text-yellow-800';
          break;
      case 'Izin':
          $classes .= 'bg-blue-100 text-blue-800';
          break;
      case 'Alfa':
          $classes .= 'bg-red-100 text-red-800';
          break;
      default:
          $classes .= 'bg-gray-100 text-gray-500';
          break;
  }

  $displayText = $status ?? 'Belum Absen';
@endphp

<span class="{{ $classes }}">
  {{ $displayText }}
</span>
