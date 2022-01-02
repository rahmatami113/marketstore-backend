<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Food') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class= "mb-10">
                <a href="{{ route('food.create') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    + Create Food
                </a>
            </div>
            <div class="bg-white">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="border px-6 py-4">ID</th>
                            <th class="border px-6 py-4">Name</th>
                            <th class="border px-6 py-4">Price</th>
                            <th class="border px-6 py-4">Rate</th>
                            <th class="border px-6 py-4">Types</th>
                            <th class="border px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($food as $item)
                        <tr>
                           <th class="border px-6 py-4">{{ $item->id }}</th>
                           <th class="border px-6 py-4">{{ $item->name }}</th>
                           <th class="border px-6 py-4">{{ number_format($item->price) }}</th>
                           <th class="border px-6 py-4">{{ $item->rate }}</th>
                           <th class="border px-6 py-4">{{ $item->types }}</th>
                           <th class="border px-6 py-4 text-center">
                           <a href="{{ route('food.edit', $item->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mx-2 rounded">
                               Edit
                             </a>
                             <form action="{{ route('food.destroy', $item->id)}}" method="POST" class="inline-block">
                                 {!! method_field('delete').csrf_field() !!}
                                 <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 mx-2 rounded">
                                     Delete
                                 </button>
                             </form>
                           </th>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border text-center p-5">
                                Data Tidak Ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!--akan ada link dibagian bawah ketika data user mencapai 10-->
            <div class="text-center mt-5">
                {{ $food->links()}}
            </div>
        </div>
    </div>
</x-app-layout>
