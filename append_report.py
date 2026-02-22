
import os

report_file = '/Applications/XAMPP/xamppfiles/htdocs/cp/CODE_REPORT.txt'
base_dir = '/Applications/XAMPP/xamppfiles/htdocs/cp'

files_to_append = [
    'newslettter.php',
    'offersmail.php',
    'products.php',
    'myorders.php',
    'wishlist.php',
    'review_action.php',
    'logout.php',
    'homeaction.php',
    'profile_modal.php',
    'error.php',
    'order_successful.php'
]

def append_files():
    with open(report_file, 'a', encoding='utf-8') as outfile:
        outfile.write('\n\n') # Ensure separation from previous content
        
        for filename in files_to_append:
            filepath = os.path.join(base_dir, filename)
            if os.path.exists(filepath):
                outfile.write(f'START FILE: {filename}\n')
                outfile.write('-' * 50 + '\n')
                
                try:
                    with open(filepath, 'r', encoding='utf-8', errors='ignore') as infile:
                        outfile.write(infile.read())
                except Exception as e:
                    outfile.write(f'Error reading file: {e}\n')
                    
                outfile.write('\n' + '-' * 50 + '\n')
                outfile.write(f'END FILE: {filename}\n\n')
                print(f"Appended {filename}")
            else:
                print(f"File not found: {filename}")

if __name__ == '__main__':
    append_files()
