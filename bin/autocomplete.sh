_dumbo() {

    local cur action
    COMPREPLY=()
    cur=`_get_cword`
    if [[ ${COMP_WORDS[1]} ]]; then
        action="${COMP_WORDS[1]}"
    else
        action=""
    fi

    _dumbo_commands="$(/usr/local/bin/dumbo autocomplete ${action})"
    COMPREPLY=( $(compgen -W "${_dumbo_commands}" -- ${cur}) )

    return 0
}
complete -o nospace -F _dumbo dumbo