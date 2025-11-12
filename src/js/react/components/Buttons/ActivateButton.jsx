import React, { useState } from 'react'
import ExternalLinkButton from './ExternalLinkButton'

const ActivateButton = () => {
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)

  return (
    <>
      <ExternalLinkButton
        type='primary'
        label='Get Started'
        icon='arrow'
        onClick={() => {
          setOpenActivationModal(true)
        }}
      />
    </>
  )
}

export default ActivateButton
