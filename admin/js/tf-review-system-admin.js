// jQuery(document).ready(function($) {
//   $('#department').on('change', function() {
//       var departmentId = $(this).val();
// console.log(departmentId);
//       $.ajax({
//           url: ajax_object.ajax_url,
//           type: 'POST',
//           data: {
//               action: 'get_department_data',
//               department_id: departmentId
//           },
//           success: function(response) {
//               if (response.success) {
//                   var listbox = $('#data_listbox');
//                   listbox.empty();
//                   $.each(response.data, function(index, item) {
//                       listbox.append($('<option>', {
//                           value: item.id,
//                           text: item.name
//                       }));
//                   });
//               } else {
//                   alert('Failed to fetch data');
//               }
//           }
//       });
//   });
// });
