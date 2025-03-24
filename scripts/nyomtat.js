function nyomtat(x) {
    print_window = window.open(x,"","width=1000px,height=600px");
    print_window.onload = () => {print_window.print(); setTimeout(() => {print_window.close();},100)};
}