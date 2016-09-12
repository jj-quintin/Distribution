import React from 'react'

export const Thumb = props =>
  <a href="#/alt-editor"
    className={props.active ? 'step-thumb thumbnail active' : 'step-thumb thumbnail'}
  >
    {props.title}
  </a>

const T = React.PropTypes

Thumb.propTypes = {
  id: T.string.isRequired,
  title: T.string.isRequired,
  active: T.bool.isRequired
}
