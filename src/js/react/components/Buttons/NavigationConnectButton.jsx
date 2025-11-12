import React, { useState } from 'react'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import InternalLinkButton from './InternalLinkButton'
import ExternalLinkButton from './ExternalLinkButton'

const NavigationConnectButton = () => {
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)
  const { subscriptionStatus } = useGlobalConfig()

  // We want to show the user they have connected their account
  if (subscriptionStatus === 'paid') {
    return (
      <InternalLinkButton type='ghost' label='Account Connected' icon='tick' href='/settings' />
    )
  }

  return (
    <>
      <ExternalLinkButton
        type='primary'
        label='Link API Key'
        icon='link'
        onClick={() => {
          setOpenActivationModal(true)
        }}
      />
    </>
  )
}

export default NavigationConnectButton
